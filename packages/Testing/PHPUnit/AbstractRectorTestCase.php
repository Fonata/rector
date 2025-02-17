<?php

declare(strict_types=1);

namespace Rector\Testing\PHPUnit;

use Iterator;
use Nette\Utils\Strings;
use PHPStan\Analyser\NodeScopeResolver;
use PHPUnit\Framework\ExpectationFailedException;
use Psr\Container\ContainerInterface;
use Rector\Core\Application\FileProcessor;
use Rector\Core\Application\FileSystem\RemovedAndAddedFilesCollector;
use Rector\Core\Bootstrap\RectorConfigsResolver;
use Rector\Core\Configuration\Option;
use Rector\Core\Contract\Rector\RectorInterface;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\HttpKernel\RectorKernel;
use Rector\Core\NonPhpFile\NonPhpFileProcessor;
use Rector\Core\PhpParser\Printer\BetterStandardPrinter;
use Rector\Core\ValueObject\StaticNonPhpFileSuffixes;
use Rector\NodeTypeResolver\Reflection\BetterReflection\SourceLocatorProvider\DynamicSourceLocatorProvider;
use Rector\Testing\Application\EnabledRectorClassProvider;
use Rector\Testing\Configuration\AllRectorConfigFactory;
use Rector\Testing\Guard\FixtureGuard;
use Rector\Testing\PHPUnit\Behavior\MovingFilesTrait;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

abstract class AbstractRectorTestCase extends AbstractKernelTestCase
{
    use MovingFilesTrait;

    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    /**
     * @var SmartFileSystem
     */
    protected static $smartFileSystem;

    /**
     * @var NonPhpFileProcessor
     */
    protected $nonPhpFileProcessor;

    /**
     * @var ParameterProvider
     */
    protected $parameterProvider;

    /**
     * @var FixtureGuard
     */
    protected static $fixtureGuard;

    /**
     * @var RemovedAndAddedFilesCollector
     */
    protected $removedAndAddedFilesCollector;

    /**
     * @var SmartFileInfo
     */
    protected $originalTempFileInfo;

    /**
     * @var ContainerInterface|null
     */
    protected static $allRectorContainer;

    /**
     * @var bool
     */
    private static $isInitialized = false;

    /**
     * @var RectorConfigsResolver
     */
    private static $rectorConfigsResolver;

    /**
     * @var BetterStandardPrinter
     */
    private $betterStandardPrinter;

    protected function setUp(): void
    {
        $this->initializeDependencies();

        if ($this->provideConfigFilePath() !== '') {
            $configFileInfo = new SmartFileInfo($this->provideConfigFilePath());
            $configFileInfos = self::$rectorConfigsResolver->resolveFromConfigFileInfo($configFileInfo);

            $this->bootKernelWithConfigsAndStaticCache(RectorKernel::class, $configFileInfos);

            /** @var EnabledRectorClassProvider $enabledRectorsProvider */
            $enabledRectorsProvider = $this->getService(EnabledRectorClassProvider::class);
            $enabledRectorsProvider->reset();
        } else {
            // prepare container with all rectors
            // cache only rector tests - defined in phpunit.xml
            $this->createRectorRepositoryContainer();

            /** @var EnabledRectorClassProvider $enabledRectorsProvider */
            $enabledRectorsProvider = $this->getService(EnabledRectorClassProvider::class);
            $enabledRectorsProvider->setEnabledRectorClass($this->getRectorClass());
        }

        $this->fileProcessor = $this->getService(FileProcessor::class);
        $this->nonPhpFileProcessor = $this->getService(NonPhpFileProcessor::class);
        $this->parameterProvider = $this->getService(ParameterProvider::class);
        $this->betterStandardPrinter = $this->getService(BetterStandardPrinter::class);

        $this->removedAndAddedFilesCollector = $this->getService(RemovedAndAddedFilesCollector::class);
        $this->removedAndAddedFilesCollector->reset();
    }

    /**
     * @return class-string<RectorInterface>
     */
    protected function getRectorClass(): string
    {
        // can be implemented
        return '';
    }

    protected function provideConfigFilePath(): string
    {
        // can be implemented
        return '';
    }

    /**
     * @return Iterator<SmartFileInfo>
     */
    protected function yieldFilesFromDirectory(string $directory, string $suffix = '*.php.inc'): Iterator
    {
        return StaticFixtureFinder::yieldDirectoryExclusively($directory, $suffix);
    }

    /**
     * @param SmartFileInfo[] $extraFileInfos
     */
    protected function doTestFileInfo(SmartFileInfo $fixtureFileInfo, array $extraFileInfos = []): void
    {
        self::$fixtureGuard->ensureFileInfoHasDifferentBeforeAndAfterContent($fixtureFileInfo);

        $inputFileInfoAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo,
            false
        );

        $inputFileInfo = $inputFileInfoAndExpectedFileInfo->getInputFileInfo();

        // needed for PHPStan, because the analyzed file is just create in /temp
        /** @var NodeScopeResolver $nodeScopeResolver */
        $nodeScopeResolver = $this->getService(NodeScopeResolver::class);
        $nodeScopeResolver->setAnalysedFiles([$inputFileInfo->getRealPath()]);

        /** @var DynamicSourceLocatorProvider $dynamicDirectoryLocatorProvider */
        $dynamicDirectoryLocatorProvider = $this->getService(DynamicSourceLocatorProvider::class);
        $dynamicDirectoryLocatorProvider->setFileInfo($inputFileInfo);

        $expectedFileInfo = $inputFileInfoAndExpectedFileInfo->getExpectedFileInfo();

        $this->doTestFileMatchesExpectedContent($inputFileInfo, $expectedFileInfo, $fixtureFileInfo, $extraFileInfos);
        $this->originalTempFileInfo = $inputFileInfo;
    }

    protected function doTestExtraFile(string $expectedExtraFileName, string $expectedExtraContentFilePath): void
    {
        $addedFilesWithContents = $this->removedAndAddedFilesCollector->getAddedFilesWithContent();
        foreach ($addedFilesWithContents as $addedFileWithContent) {
            if (! Strings::endsWith($addedFileWithContent->getFilePath(), $expectedExtraFileName)) {
                continue;
            }

            $this->assertStringEqualsFile($expectedExtraContentFilePath, $addedFileWithContent->getFileContent());
            return;
        }

        $addedFilesWithNodes = $this->removedAndAddedFilesCollector->getAddedFilesWithNodes();
        foreach ($addedFilesWithNodes as $addedFileWithNode) {
            if (! Strings::endsWith($addedFileWithNode->getFilePath(), $expectedExtraFileName)) {
                continue;
            }

            $printedFileContent = $this->betterStandardPrinter->prettyPrintFile($addedFileWithNode->getNodes());
            $this->assertStringEqualsFile($expectedExtraContentFilePath, $printedFileContent);
            return;
        }

        $movedFilesWithContent = $this->removedAndAddedFilesCollector->getMovedFileWithContent();
        foreach ($movedFilesWithContent as $movedFileWithContent) {
            if (! Strings::endsWith($movedFileWithContent->getNewPathname(), $expectedExtraFileName)) {
                continue;
            }

            $this->assertStringEqualsFile($expectedExtraContentFilePath, $movedFileWithContent->getFileContent());
            return;
        }

        throw new ShouldNotHappenException();
    }

    protected function getFixtureTempDirectory(): string
    {
        return sys_get_temp_dir() . '/_temp_fixture_easy_testing';
    }

    private function createRectorRepositoryContainer(): void
    {
        if (self::$allRectorContainer === null) {
            $allRectorConfigFactory = new AllRectorConfigFactory();
            $configFilePath = $allRectorConfigFactory->create();
            $this->bootKernelWithConfigs(RectorKernel::class, [$configFilePath]);

            self::$allRectorContainer = self::$container;
            return;
        }

        // load from cache
        self::$container = self::$allRectorContainer;
    }

    /**
     * @param SmartFileInfo[] $extraFileInfos
     */
    private function doTestFileMatchesExpectedContent(
        SmartFileInfo $originalFileInfo,
        SmartFileInfo $expectedFileInfo,
        SmartFileInfo $fixtureFileInfo,
        array $extraFileInfos = []
    ): void {
        $this->parameterProvider->changeParameter(Option::SOURCE, [$originalFileInfo->getRealPath()]);

        if (! Strings::endsWith($originalFileInfo->getFilename(), '.blade.php') && in_array(
            $originalFileInfo->getSuffix(),
            ['php', 'phpt'],
            true
        )) {
            if ($extraFileInfos === []) {
                $this->fileProcessor->parseFileInfoToLocalCache($originalFileInfo);
                $this->fileProcessor->refactor($originalFileInfo);
                $this->fileProcessor->postFileRefactor($originalFileInfo);
            } else {
                $fileInfosToProcess = array_merge([$originalFileInfo], $extraFileInfos);

                // life-cycle trio :)
                foreach ($fileInfosToProcess as $fileInfoToProcess) {
                    $this->fileProcessor->parseFileInfoToLocalCache($fileInfoToProcess);
                }

                foreach ($fileInfosToProcess as $fileInfoToProcess) {
                    $this->fileProcessor->refactor($fileInfoToProcess);
                }

                foreach ($fileInfosToProcess as $fileInfoToProcess) {
                    $this->fileProcessor->postFileRefactor($fileInfoToProcess);
                }
            }

            // mimic post-rectors
            $changedContent = $this->fileProcessor->printToString($originalFileInfo);
        } elseif (Strings::match($originalFileInfo->getFilename(), StaticNonPhpFileSuffixes::getSuffixRegexPattern())) {
            $changedContent = $this->nonPhpFileProcessor->processFileInfo($originalFileInfo);
        } else {
            $message = sprintf('Suffix "%s" is not supported yet', $originalFileInfo->getSuffix());
            throw new ShouldNotHappenException($message);
        }

        $relativeFilePathFromCwd = $fixtureFileInfo->getRelativeFilePathFromCwd();

        try {
            $this->assertStringEqualsFile($expectedFileInfo->getRealPath(), $changedContent, $relativeFilePathFromCwd);
        } catch (ExpectationFailedException $expectationFailedException) {
            StaticFixtureUpdater::updateFixtureContent($originalFileInfo, $changedContent, $fixtureFileInfo);
            $contents = $expectedFileInfo->getContents();

            // make sure we don't get a diff in which every line is different (because of differences in EOL)
            $contents = $this->normalizeNewlines($contents);

            // if not exact match, check the regex version (useful for generated hashes/uuids in the code)
            $this->assertStringMatchesFormat($contents, $changedContent, $relativeFilePathFromCwd);
        }
    }

    private function normalizeNewlines(string $string): string
    {
        return Strings::replace($string, '#\r\n|\r|\n#', "\n");
    }

    /**
     * Static to avoid reboot on each data fixture
     */
    private function initializeDependencies(): void
    {
        if (self::$isInitialized) {
            return;
        }

        self::$smartFileSystem = new SmartFileSystem();
        self::$fixtureGuard = new FixtureGuard();
        self::$rectorConfigsResolver = new RectorConfigsResolver();
        self::$isInitialized = true;
    }
}
