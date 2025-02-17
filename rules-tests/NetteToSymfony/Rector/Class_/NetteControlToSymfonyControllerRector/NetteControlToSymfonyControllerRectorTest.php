<?php

declare(strict_types=1);

namespace Rector\Tests\NetteToSymfony\Rector\Class_\NetteControlToSymfonyControllerRector;

use Iterator;
use Rector\NetteToSymfony\Rector\Class_\NetteControlToSymfonyControllerRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NetteControlToSymfonyControllerRectorTest extends AbstractRectorTestCase
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
        return NetteControlToSymfonyControllerRector::class;
    }
}
