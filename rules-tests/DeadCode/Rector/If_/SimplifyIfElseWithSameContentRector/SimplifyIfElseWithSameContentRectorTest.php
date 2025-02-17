<?php

declare(strict_types=1);

namespace Rector\Tests\DeadCode\Rector\If_\SimplifyIfElseWithSameContentRector;

use Iterator;
use Rector\DeadCode\Rector\If_\SimplifyIfElseWithSameContentRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SimplifyIfElseWithSameContentRectorTest extends AbstractRectorTestCase
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
        return SimplifyIfElseWithSameContentRector::class;
    }
}
