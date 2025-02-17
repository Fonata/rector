<?php

declare(strict_types=1);

namespace Rector\Tests\Restoration\Rector\Class_\RemoveFinalFromEntityRector;

use Iterator;
use Rector\Restoration\Rector\Class_\RemoveFinalFromEntityRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class RemoveFinalFromEntityRectorTest extends AbstractRectorTestCase
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
        return RemoveFinalFromEntityRector::class;
    }
}
