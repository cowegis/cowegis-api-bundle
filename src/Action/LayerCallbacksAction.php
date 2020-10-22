<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\Action;

use Cowegis\Core\Definition\Layer\LayerId;
use Cowegis\Core\Definition\Map\MapId;
use Cowegis\Core\Filter\FilterFactory;
use Cowegis\Core\Provider\LayerDataContext;
use Cowegis\Core\Provider\Provider;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LayerCallbacksAction
{
    /** @var Provider */
    private $provider;

    /**
     * @var FilterFactory
     */
    private $filterFactory;
    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    public function __construct(Provider $mapProvider, FilterFactory $filterFactory, UriFactoryInterface $uriFactory)
    {
        $this->provider      = $mapProvider;
        $this->filterFactory = $filterFactory;
        $this->uriFactory    = $uriFactory;
    }

    public function __invoke(string $mapId, string $layerId, Request $request): Response
    {
        $mapId   = $this->provider->idFormat()->createDefinitionId(MapId::class, $mapId);
        $layerId = $this->provider->idFormat()->createDefinitionId(LayerId::class, $layerId);
        $filter  = $this->filterFactory->createFromUri($this->uriFactory->createUri($request->getUri()));
        $locale  = $request->getLocale();
        $context = LayerDataContext::create($filter, $mapId, $layerId, $locale);

        $this->provider->findLayerData($mapId, $layerId, $context);

        return new Response($context->callbacks()->asJavascript(), 200, ['Content-Type' => 'application/javascript']);
    }
}
