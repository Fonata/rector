<?php

namespace Rector\Tests\DeadCode\Rector\ClassMethod\RemoveUnusedParameterRector\FixtureOpenSource;

abstract class SkipAbstract
{
    public abstract function foo(string $foo, string $bar);
}

class UsingAbstract extends SkipAbstract
{
    public function foo(string $foo, string $bar)
    {
        return $foo;
    }
}
