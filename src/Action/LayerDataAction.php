<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\Action;

use Cowegis\Core\Definition\Asset\Asset;
use Cowegis\Core\Definition\Layer\LayerId;
use Cowegis\Core\Definition\Map\MapId;
use Cowegis\Core\Filter\FilterFactory;
use Cowegis\Core\Provider\LayerDataContext;
use Cowegis\Core\Provider\Provider;
use Cowegis\Core\Serializer\Serializer;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

use function count;

final class LayerDataAction
{
    /** @var Provider */
    private $provider;

    /** @var FilterFactory */
    private $filterFactory;

    /** @var Serializer */
    private $serializer;
    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        Provider $provider,
        FilterFactory $filterFactory,
        Serializer $serializer,
        UriFactoryInterface $uriFactory,
        RouterInterface $router
    ) {
        $this->provider      = $provider;
        $this->serializer    = $serializer;
        $this->filterFactory = $filterFactory;
        $this->uriFactory    = $uriFactory;
        $this->router        = $router;
    }

    public function __invoke(string $mapId, string $layerId, Request $request): Response
    {
        $mapId     = $this->provider->idFormat()->createDefinitionId(MapId::class, $mapId);
        $layerId   = $this->provider->idFormat()->createDefinitionId(LayerId::class, $layerId);
        $filter    = $this->filterFactory->createFromUri($this->uriFactory->createUri($request->getUri()));
        $locale    = $request->getLocale();
        $context   = LayerDataContext::create($filter, $mapId, $layerId, $locale);
        $layerData = $this->provider->findLayerData($mapId, $layerId, $context);

        if (count($context->callbacks()) > 0) {
            $callbacksUrl = $this->router->generate(
                'cowegis_api_js_layer_callbacks',
                ['mapId' => $mapId->value(), 'layerId' => $layerId->value()]
            );

            $context->assets()->add(Asset::CALLBACKS($context->callbacks()->identifier(), $callbacksUrl));
        }

        return new JsonResponse(
            [
                'data'   => $this->serializer->serialize($layerData),
                'assets' => $this->serializer->serialize($context->assets()->toArray()),
            ]
        );
    }
}
