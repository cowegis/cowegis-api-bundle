<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder() : TreeBuilder
    {
        $builder = new TreeBuilder('cowegis');
        $rootNode = $builder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('api')
                    ->children()
                        ->scalarNode('version')
                            ->info('API version. Cowegis has a dynamic API schema caused by the plugin structure. You may force a version')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('routing')
                    ->children()
                        ->scalarNode('prefix')
                            ->info('Route prefix')
                            ->defaultValue('cowegis')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}
