<?php

declare(strict_types=1);

namespace Rector\Tests\Laravel\Rector\MethodCall\ChangeQueryWhereDateValueWithCarbonRector;

use Iterator;
use Rector\Laravel\Rector\MethodCall\ChangeQueryWhereDateValueWithCarbonRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ChangeQueryWhereDateValueWithCarbonRectorTest extends AbstractRectorTestCase
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
        return ChangeQueryWhereDateValueWithCarbonRector::class;
    }
}
