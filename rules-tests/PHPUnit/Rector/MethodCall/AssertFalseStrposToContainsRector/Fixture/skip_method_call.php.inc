<?php

namespace Rector\Tests\PHPUnit\Rector\MethodCall\AssertFalseStrposToContainsRector\Fixture;

final class SkipMethodCall extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $someObject = new Foo();
        self::assertFalse($someObject->someMethod());
    }
}
