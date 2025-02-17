<?php

declare(strict_types=1);

namespace Rector\Tests\PHPUnit\Rector\MethodCall\WithConsecutiveArgToArrayRector\Source;

final class ClassWithMethodOfTwoArguments
{
    public function go(int $one, string $two): void
    {
        $three = $one + $two;
    }
}
