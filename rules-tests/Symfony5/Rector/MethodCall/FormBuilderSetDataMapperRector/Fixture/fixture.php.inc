<?php

namespace Rector\Tests\Symfony5\Rector\MethodCall\FormBuilderSetDataMapperRector\Fixture;

use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\Form\FormConfigBuilderInterface;

final class Fixture
{
    public function run(FormConfigBuilderInterface $builder)
    {
        $builder->setDataMapper(new PropertyPathMapper());
    }
}
-----
<?php

namespace Rector\Tests\Symfony5\Rector\MethodCall\FormBuilderSetDataMapperRector\Fixture;

use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\Form\FormConfigBuilderInterface;

final class Fixture
{
    public function run(FormConfigBuilderInterface $builder)
    {
        $builder->setDataMapper(new \Symfony\Component\Form\Extension\Core\DataMapper\DataMapper(new \Symfony\Component\Form\Extension\Core\DataAccessor\PropertyPathAccessor()));
    }
}
