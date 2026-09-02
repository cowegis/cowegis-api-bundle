<?php

declare(strict_types=1);

namespace spec\Cowegis\Bundle\Api\DependencyInjection;

use Cowegis\Bundle\Api\DependencyInjection\Configuration;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Definition\Processor;

use function is_string;

final class ConfigurationSpec extends ObjectBehavior
{
    public function it_defaults_the_version_to_the_installed_package_version(): void
    {
        $config = $this->process([]);

        expect($config['api']['version'])->notToBe('latest');
        expect(is_string($config['api']['version']) && $config['api']['version'] !== '')->toBe(true);
    }

    public function it_keeps_a_pinned_version_verbatim(): void
    {
        $config = $this->process([['api' => ['version' => '2.5.0']]]);

        expect($config['api']['version'])->toBe('2.5.0');
    }

    /**
     * @param list<array<array-key, mixed>> $configs
     *
     * @return array{api: array{prefix: string, version: string}}
     */
    private function process(array $configs): array
    {
        /** @var array{api: array{prefix: string, version: string}} $processed */
        $processed = (new Processor())->processConfiguration($this->getWrappedObject(), $configs);

        return $processed;
    }
}
