<?php

namespace Rector\Tests\Symfony\Rector\MethodCall\GetToConstructorInjectionRector\Fixture;

use Rector\Tests\Symfony\Rector\Source\SymfonyController;

class ClassWithNamedService extends SymfonyController
{
    public function render()
    {
        $someService = $this->get('some_service');

        $this->renderTwig([
            'posts' => $this->get('some_service')->callMe()
        ]);
    }
}

?>
-----
<?php

namespace Rector\Tests\Symfony\Rector\MethodCall\GetToConstructorInjectionRector\Fixture;

use Rector\Tests\Symfony\Rector\Source\SymfonyController;

class ClassWithNamedService extends SymfonyController
{
    public function __construct(private \stdClass $stdClass)
    {
    }
    public function render()
    {
        $someService = $this->stdClass;

        $this->renderTwig([
            'posts' => $this->stdClass->callMe()
        ]);
    }
}

?>
