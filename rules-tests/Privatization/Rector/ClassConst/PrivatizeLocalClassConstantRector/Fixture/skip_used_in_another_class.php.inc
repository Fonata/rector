<?php

namespace Rector\Tests\Privatization\Rector\ClassConst\PrivatizeLocalClassConstantRector\Fixture;

class SkipUsedInAnotherClass
{
    private const SOME = ConstainsConstant::KEEP_PUBLIC . '_pcs';
}

class ConstainsConstant
{
    public const KEEP_PUBLIC = 'cms';
}
