<?php

declare(strict_types=1);

namespace Rector\Tests\DowngradePhp74\Rector\Property\DowngradeTypedPropertyRector;

use Iterator;
use Rector\DowngradePhp74\Rector\Property\DowngradeTypedPropertyRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class DowngradeTypedPropertyRectorTest extends AbstractRectorTestCase
{
    /**
     * @requires PHP 7.4
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
        return DowngradeTypedPropertyRector::class;
    }
}
