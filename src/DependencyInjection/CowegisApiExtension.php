<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

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
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');
        $loader->load('schema.xml');
        $loader->load('filter.xml');
        $loader->load('serializer.xml');

        /** @psalm-var TProcessedConfig $config */
        $config = $this->processConfiguration(new Configuration(), $configs);
        $prefix = $config['api']['prefix'];

        $container->setParameter('cowegis_api.api_version', $config['api']['version']);
        $container->setParameter('cowegis_api.route_prefix', $prefix);
        $container->setParameter('cowegis_api.api_base_uri', $prefix . '/api');
    }
}
