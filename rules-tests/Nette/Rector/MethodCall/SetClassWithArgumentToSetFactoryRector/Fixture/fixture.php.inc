<?php

namespace Rector\Tests\Nette\Rector\MethodCall\SetClassWithArgumentToSetFactoryRector\Fixture;

use Nette\DI\ContainerBuilder;

class SomeClass
{
    public function run(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addDefinition('...')
            ->setClass('SomeClass', [1, 2]);
    }
}

?>
-----
<?php

namespace Rector\Tests\Nette\Rector\MethodCall\SetClassWithArgumentToSetFactoryRector\Fixture;

use Nette\DI\ContainerBuilder;

class SomeClass
{
    public function run(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addDefinition('...')
            ->setFactory('SomeClass', [1, 2]);
    }
}

?>
