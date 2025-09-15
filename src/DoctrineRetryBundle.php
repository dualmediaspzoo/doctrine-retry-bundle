<?php

namespace DualMedia\DoctrineRetryBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class DoctrineRetryBundle extends AbstractBundle
{
    protected string $extensionAlias = 'dm_doctrine_retry';

    public function configure(
        DefinitionConfigurator $definition
    ): void {
        $definition->rootNode() // @phpstan-ignore-line
            ->children()
                ->booleanNode('track_nesting')
                ->defaultValue('%kernel.debug%')
                ->end()
            ->end();
    }

    /**
     * @param array<string, mixed> $config
     */
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder
    ): void {
        $loader = new PhpFileLoader(
            $builder,
            new FileLocator(__DIR__.'/../config')
        );

        $loader->load('services.php');

        $builder->setParameter('.dualmedia.doctrine_retry.track_nesting', $config['track_nesting']);
    }
}
