<?php

declare(strict_types=1);

namespace Rector\Tests\NetteCodeQuality\Rector\Assign\ArrayAccessGetControlToGetComponentMethodCallRector;

use Iterator;
use Rector\NetteCodeQuality\Rector\Assign\ArrayAccessGetControlToGetComponentMethodCallRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ArrayAccessGetControlToGetComponentMethodCallRectorTest extends AbstractRectorTestCase
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
        return ArrayAccessGetControlToGetComponentMethodCallRector::class;
    }
}
