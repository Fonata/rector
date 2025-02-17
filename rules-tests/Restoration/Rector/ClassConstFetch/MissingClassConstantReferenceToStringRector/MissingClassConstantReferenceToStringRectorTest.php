<?php

declare(strict_types=1);

namespace Rector\Tests\Restoration\Rector\ClassConstFetch\MissingClassConstantReferenceToStringRector;

use Iterator;
use Rector\Restoration\Rector\ClassConstFetch\MissingClassConstantReferenceToStringRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class MissingClassConstantReferenceToStringRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    /**
     * @return Iterator<SmartFileInfo>
     */
    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    protected function getRectorClass(): string
    {
        return MissingClassConstantReferenceToStringRector::class;
    }
}
