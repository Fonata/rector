<?php

namespace Rector\Tests\DeadDocBlock\Rector\Property\RemoveUselessVarTagRector\Fixture;

final class SomeClass
{
    /**
     * @var string
     */
    public string $name = 'name';
}

?>
-----
<?php

namespace Rector\Tests\DeadDocBlock\Rector\Property\RemoveUselessVarTagRector\Fixture;

final class SomeClass
{
    public string $name = 'name';
}

?>
