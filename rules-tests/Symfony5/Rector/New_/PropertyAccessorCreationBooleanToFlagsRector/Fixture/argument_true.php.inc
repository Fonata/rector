<?php

namespace Rector\Tests\Symfony5\Rector\New_\PropertyAccessorCreationBooleanToFlagsRector\Fixture;

use Symfony\Component\PropertyAccess\PropertyAccessor;

class ArgumentTrue
{
    public function createPropertyAccessor()
    {
        return new PropertyAccessor(true);
    }
}

?>
-----
<?php

namespace Rector\Tests\Symfony5\Rector\New_\PropertyAccessorCreationBooleanToFlagsRector\Fixture;

use Symfony\Component\PropertyAccess\PropertyAccessor;

class ArgumentTrue
{
    public function createPropertyAccessor()
    {
        return new PropertyAccessor(\Symfony\Component\PropertyAccess\PropertyAccessor::MAGIC_CALL | \Symfony\Component\PropertyAccess\PropertyAccessor::MAGIC_GET | \Symfony\Component\PropertyAccess\PropertyAccessor::MAGIC_SET);
    }
}

?>
