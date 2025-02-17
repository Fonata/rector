<?php

declare(strict_types=1);

namespace Rector\Tests\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector;

use Iterator;
use Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LogicalToBooleanRectorTest extends AbstractRectorTestCase
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
        return LogicalToBooleanRector::class;
    }
}
