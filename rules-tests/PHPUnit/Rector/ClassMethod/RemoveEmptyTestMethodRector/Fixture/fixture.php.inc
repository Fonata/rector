<?php

namespace Rector\Tests\PHPUnit\Rector\ClassMethod\RemoveEmptyTestMethodRector\Fixture;

class SomeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * testGetTranslatedModelField method
     *
     * @return void
     */
    public function testGetTranslatedModelField()
    {
    }
}

?>
-----
<?php

namespace Rector\Tests\PHPUnit\Rector\ClassMethod\RemoveEmptyTestMethodRector\Fixture;

class SomeTest extends \PHPUnit\Framework\TestCase
{
}

?>
