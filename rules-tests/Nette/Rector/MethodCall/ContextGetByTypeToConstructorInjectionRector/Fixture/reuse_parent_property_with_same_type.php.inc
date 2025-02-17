<?php

namespace Rector\Tests\Nette\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Fixture;

use Rector\Tests\Nette\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ParentWithSomeTypeToInjectPropertyPresenter;
use Rector\Tests\Nette\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\SomeTypeToInject;

class ReuseParentPropertyWithSameType extends ParentWithSomeTypeToInjectPropertyPresenter
{
    /**
     * @var \Nette\DI\Container
     */
    private $context;

    public function go()
    {
        $someTypeToInject = $this->context->getByType(SomeTypeToInject::class);
    }
}

?>
-----
<?php

namespace Rector\Tests\Nette\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Fixture;

use Rector\Tests\Nette\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ParentWithSomeTypeToInjectPropertyPresenter;
use Rector\Tests\Nette\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\SomeTypeToInject;

class ReuseParentPropertyWithSameType extends ParentWithSomeTypeToInjectPropertyPresenter
{
    /**
     * @var \Nette\DI\Container
     */
    private $context;

    public function go()
    {
        $someTypeToInject = $this->someTypeToInject;
    }
}

?>
