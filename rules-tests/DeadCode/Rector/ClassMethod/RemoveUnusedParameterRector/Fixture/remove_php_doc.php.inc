<?php

namespace Rector\Tests\DeadCode\Rector\ClassMethod\RemoveUnusedParameterRector\Fixture;

final class RemovePhpDoc
{
    /**
     * @param int $notUsed interesting comment
     */
    public function run(int $notUsed)
    {

    }
}

?>
-----
<?php

namespace Rector\Tests\DeadCode\Rector\ClassMethod\RemoveUnusedParameterRector\Fixture;

final class RemovePhpDoc
{
    public function run()
    {
    }
}

?>
