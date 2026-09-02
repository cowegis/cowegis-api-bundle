<?php

declare(strict_types=1);

namespace spec\Cowegis\Bundle\Api\Action;

use Cowegis\Bundle\Api\Action\SchemaAction;
use Cowegis\Core\Schema\DelegatingSchemaDescriber;
use Cowegis\Core\Schema\Error\ErrorSchemaDescriber;
use Cowegis\Core\Schema\Id\IntegerIdSchema;
use Cowegis\Core\Schema\SchemaDescriber;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;

use function is_string;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final class SchemaActionSpec extends ObjectBehavior
{
    public function let(SchemaDescriber $describer): void
    {
        $this->beConstructedWith($describer, [new IntegerIdSchema()], 'cowegis/api', '1.2.3');
    }

    public function it_emits_the_configured_version_verbatim(): void
    {
        $doc = $this->decode($this->__invoke(Request::create('https://example.com/cowegis/docs/schema.json')));

        expect($doc['info']['version'])->toBe('1.2.3');
        expect($doc['openapi'])->toBe('3.0.2');
        expect($doc)->toHaveKey('components');
    }

    public function it_never_emits_the_latest_sentinel(SchemaDescriber $describer): void
    {
        $this->beConstructedWith($describer, [new IntegerIdSchema()], 'cowegis/api', 'latest');

        $doc = $this->decode($this->__invoke(Request::create('https://example.com/cowegis/docs/schema.json')));

        expect($doc['info']['version'])->notToBe('latest');
        expect(is_string($doc['info']['version']) && $doc['info']['version'] !== '')->toBe(true);
    }

    public function it_exposes_the_error_schema_component(): void
    {
        $this->beConstructedWith(
            new DelegatingSchemaDescriber([new ErrorSchemaDescriber()]),
            [new IntegerIdSchema()],
            'cowegis/api',
            '1.2.3',
        );

        $doc = $this->decode($this->__invoke(Request::create('https://example.com/cowegis/docs/schema.json')));

        expect($doc['components']['schemas'])->toHaveKey('Error');
    }

    /** @return array<string, mixed> */
    private function decode(object $response): array
    {
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode((string) $response->getWrappedObject()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }
}
