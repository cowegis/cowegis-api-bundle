<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder  = new TreeBuilder('cowegis');
        $rootNode = $builder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('api')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('version')
                            ->info('API version reported in the generated OpenAPI document. The default'
                                . ' "latest" is resolved at runtime to the installed cowegis/cowegis-api-bundle'
                                . ' version. Set a fixed string to pin it.')
                            ->defaultValue('latest')
                        ->end()
                        ->scalarNode('prefix')
                            ->info('Api prefix')
                            ->defaultValue('cowegis')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}
