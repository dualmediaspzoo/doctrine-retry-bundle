<?php

use Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->private();

    $services->set(\DualMedia\DoctrineRetryBundle\Retrier::class)
        ->arg('$registry', new Reference(\Doctrine\Persistence\ManagerRegistry::class))
        ->arg('$logger', new Reference('logger', ContainerInterface::NULL_ON_INVALID_REFERENCE))
        ->arg('$trackNesting', new AbstractArgument('Will be set via bundle configuration'));
};
