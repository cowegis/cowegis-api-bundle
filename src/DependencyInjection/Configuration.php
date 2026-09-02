<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\DependencyInjection;

use Composer\InstalledVersions;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    private const string PACKAGE_NAME = 'cowegis/cowegis-api-bundle';

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
                            ->info('API version reported in the generated OpenAPI document. Defaults to the'
                                . ' installed cowegis/cowegis-api-bundle version. Set a fixed string to pin it.')
                            ->defaultValue($this->installedVersion())
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

    private function installedVersion(): string
    {
        return InstalledVersions::getPrettyVersion(self::PACKAGE_NAME) ?? 'dev';
    }
}
