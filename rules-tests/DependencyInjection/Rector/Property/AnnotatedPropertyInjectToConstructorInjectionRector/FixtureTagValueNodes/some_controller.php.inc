<?php

namespace Rector\Tests\DependencyInjection\Rector\Property\AnnotatedPropertyInjectToConstructorInjectionRector\FixtureTagValueNodes;

use JMS\DiExtraBundle\Annotation as DI;

final class SomeController
{
    /**
     * @DI\Inject("entity.manager")
     */
    private $entityManager;

    /**
     * @DI\Inject("translator", strict = false, required = false)
     */
    private $translator;
}

?>
-----
<?php

namespace Rector\Tests\DependencyInjection\Rector\Property\AnnotatedPropertyInjectToConstructorInjectionRector\FixtureTagValueNodes;

use JMS\DiExtraBundle\Annotation as DI;

final class SomeController
{
    public function __construct(private \Rector\Tests\Symfony\Rector\MethodCall\AbstractToConstructorInjectionRectorSource\SomeEntityManager $entityManager, private \Rector\Tests\Symfony\Rector\MethodCall\AbstractToConstructorInjectionRectorSource\Contract\SomeTranslatorInterface $translator)
    {
    }
}

?>
