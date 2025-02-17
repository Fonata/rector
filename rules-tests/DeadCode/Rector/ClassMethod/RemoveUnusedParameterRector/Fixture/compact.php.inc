<?php

namespace Rector\Tests\DeadCode\Rector\ClassMethod\RemoveUnusedParameterRector\Fixture;

class Compact
{
    public function keepThis($group, $missed)
    {
        compact('group');
    }

    public function keepThat($group, $pesos)
    {
        $value = 'group';
        compact($value, 'pesos');
    }
}

?>
-----
<?php

namespace Rector\Tests\DeadCode\Rector\ClassMethod\RemoveUnusedParameterRector\Fixture;

class Compact
{
    public function keepThis($group)
    {
        compact('group');
    }

    public function keepThat($group, $pesos)
    {
        $value = 'group';
        compact($value, 'pesos');
    }
}

?>
