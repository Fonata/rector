<?php

declare(strict_types=1);

use Rector\Symfony4\Rector\MethodCall\SimplifyWebTestCaseAssertionsRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SimplifyWebTestCaseAssertionsRector::class);
};
