<?php

declare(strict_types=1);

namespace Rector\Tests\Naming\Rector\Variable\UnderscoreToCamelCaseLocalVariableNameRector;

use Iterator;
use Rector\Naming\Rector\Variable\UnderscoreToCamelCaseLocalVariableNameRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class UnderscoreToCamelCaseLocalVariableNameRectorTest extends AbstractRectorTestCase
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
        return UnderscoreToCamelCaseLocalVariableNameRector::class;
    }
}
