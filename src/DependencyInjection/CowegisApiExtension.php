<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\DependencyInjection;

use Override;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @psalm-type TProcessedConfig = array{
 *   api: array{
 *      prefix: string,
 *      version: string
 *   }
 * }
 */
final class CowegisApiExtension extends Extension
{
    /**
     * @param mixed[][] $configs
     * @psalm-param list<array<array-key, mixed>> $configs
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    #[Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yaml');
        $loader->load('schema.yaml');
        $loader->load('filter.yaml');
        $loader->load('serializer.yaml');

        /** @psalm-var TProcessedConfig $config */
        $config = $this->processConfiguration(new Configuration(), $configs);
        $prefix = $config['api']['prefix'];

        $container->setParameter('cowegis_api.api_version', $config['api']['version']);
        $container->setParameter('cowegis_api.route_prefix', $prefix);
        $container->setParameter('cowegis_api.api_base_uri', $prefix . '/api');
    }
}
