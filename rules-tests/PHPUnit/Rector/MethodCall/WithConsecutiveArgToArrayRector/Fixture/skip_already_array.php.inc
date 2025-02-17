<?php

namespace Rector\Tests\PHPUnit\Rector\MethodCall\WithConsecutiveArgToArrayRector\Fixture;

use PHPUnit\Framework\TestCase;
use Rector\Tests\PHPUnit\Rector\MethodCall\WithConsecutiveArgToArrayRector\Source\ClassWithMethodOfTwoArguments;

class SkipAlreadyArrayTestCase extends TestCase
{
    public function test()
    {
        $someClassMock = $this->createMock(ClassWithMethodOfTwoArguments::class);
        $someClassMock->expects($this->exactly(2))
            ->method('run')
            ->withConsecutive([1, 2, 3, 5]);
    }
}
