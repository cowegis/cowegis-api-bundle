<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\Action;

use Cowegis\Core\Exception\RuntimeException;
use Cowegis\Core\Schema\IdSchema;
use Cowegis\Core\Schema\SchemaBuilder;
use Cowegis\Core\Schema\SchemaDescriber;
use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Info;
use GoldSpecDigital\ObjectOrientedOAS\Objects\OneOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Server;
use GoldSpecDigital\ObjectOrientedOAS\OpenApi;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Traversable;

use function array_values;
use function count;
use function is_array;
use function iterator_to_array;

use const JSON_UNESCAPED_SLASHES;

final class SchemaAction
{
    private SchemaDescriber $schemaBuilder;

    /**
     * @var IdSchema[]
     * @psalm-var list<IdSchema>
     */
    private array $idSchemas;

    private string $baseUri;

    private string $apiVersion;

    /**
     * @param IdSchema[]|Traversable $idSchemas
     * @psalm-param list<IdSchema>|Traversable<IdSchema> $idSchemas
     */
    public function __construct(
        SchemaDescriber $schemaBuilder,
        iterable $idSchemas,
        string $baseUri,
        string $apiVersion
    ) {
        $this->schemaBuilder = $schemaBuilder;
        $this->idSchemas     = array_values(is_array($idSchemas) ? $idSchemas : iterator_to_array($idSchemas));
        $this->baseUri       = $baseUri;
        $this->apiVersion    = $apiVersion;
    }

    public function __invoke(Request $request): Response
    {
        $info = Info::create()
            ->title('Cowegis API')
            ->description('Cowegis map API')
            ->version($this->apiVersion);

        $builder = SchemaBuilder::create($info, $this->idSchema(), OpenApi::OPENAPI_3_0_2);
        $this->schemaBuilder->describe($builder);

        $schema = $builder->build();

        if ($schema->servers === null) {
            $schema = $schema->servers(
                Server::create()->url($request->getSchemeAndHttpHost() . '/' . $this->baseUri)
            );
        }

        $response = new JsonResponse($schema);
        $response->setEncodingOptions(JsonResponse::DEFAULT_ENCODING_OPTIONS | JSON_UNESCAPED_SLASHES);

        return $response;
    }

    private function idSchema(): SchemaContract
    {
        switch (count($this->idSchemas)) {
            case 0:
                throw new RuntimeException('No supported id schemas found');

            case 1:
                return $this->idSchemas[0];

            default:
                return OneOf::create()
                    ->schemas(...$this->idSchemas);
        }
    }
}
