<?php

declare(strict_types=1);

namespace Rector\Tests\PHPOffice\Rector\StaticCall\ChangeDataTypeForValueRector;

use Iterator;
use Rector\PHPOffice\Rector\StaticCall\ChangeDataTypeForValueRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ChangeDataTypeForValueRectorTest extends AbstractRectorTestCase
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
        return ChangeDataTypeForValueRector::class;
    }
}
