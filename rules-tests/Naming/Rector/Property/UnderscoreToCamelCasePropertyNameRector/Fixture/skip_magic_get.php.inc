<?php

namespace Rector\Tests\Naming\Rector\Property\UnderscoreToCamelCasePropertyNameRector\Fixture;

class SkipMagicGet
{
    private $underscore_value;

    public function get()
    {
        $this->underscore_value = 5;
    }

    public function __get($key)
    {
    }
}
