<?php

declare(strict_types=1);

namespace Rector\Tests\DoctrineGedmoToKnplabs\Rector\Class_\SluggableBehaviorRector;

use Iterator;
use Rector\DoctrineGedmoToKnplabs\Rector\Class_\SluggableBehaviorRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SluggableBehaviorRectorTest extends AbstractRectorTestCase
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
        return SluggableBehaviorRector::class;
    }
}
