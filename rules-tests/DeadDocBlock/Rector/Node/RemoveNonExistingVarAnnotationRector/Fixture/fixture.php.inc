<?php

namespace Rector\Tests\DeadDocBlock\Rector\Node\RemoveNonExistingVarAnnotationRector\Fixture;

class Fixture
{
    public function get()
    {
        /** @var Training[] $trainings */
        return $this->getData();
    }
}

?>
-----
<?php

namespace Rector\Tests\DeadDocBlock\Rector\Node\RemoveNonExistingVarAnnotationRector\Fixture;

class Fixture
{
    public function get()
    {
        return $this->getData();
    }
}

?>
