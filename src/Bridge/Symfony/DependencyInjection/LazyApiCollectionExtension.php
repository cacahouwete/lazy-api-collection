<?php

declare(strict_types=1);

namespace LazyApiCollection\Bridge\Symfony\DependencyInjection;

use LazyApiCollection\Bridge\Symfony\Builder\LazyApiCollectionBuilder;
use LazyApiCollection\Bridge\Symfony\Builder\LazyApiCollectionBuilderInterface;
use LazyApiCollection\Bridge\Symfony\Serializer\Denormalizer\ApiCollectionHydraDenormalizer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class LazyApiCollectionExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $hydraDenormalizerDefinition = new Definition(
            ApiCollectionHydraDenormalizer::class
        );
        $hydraDenormalizerDefinition->setAutowired(true)->setAutoconfigured(true);
        $container->addDefinitions([ApiCollectionHydraDenormalizer::class => $hydraDenormalizerDefinition]);

        $lazyBuilderDefinition = new Definition(
            LazyApiCollectionBuilder::class
        );
        $lazyBuilderDefinition->setAutowired(true)->setAutoconfigured(true);
        $container->addDefinitions([LazyApiCollectionBuilder::class => $lazyBuilderDefinition]);
        $container->addAliases([LazyApiCollectionBuilderInterface::class => LazyApiCollectionBuilder::class]);
    }
}
