<?php

namespace Rector\Tests\Naming\Rector\Variable\UnderscoreToCamelCaseVariableNameRector\Fixture;

final class FirstUnderscore
{
    public function run($_a)
    {
        $_some_value = $_a;
    }
}
-----
<?php

namespace Rector\Tests\Naming\Rector\Variable\UnderscoreToCamelCaseVariableNameRector\Fixture;

final class FirstUnderscore
{
    public function run($a)
    {
        $someValue = $a;
    }
}
