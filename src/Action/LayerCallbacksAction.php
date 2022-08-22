<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\Action;

use Cowegis\Bundle\Api\Event\LayerResponseEvent;
use Cowegis\Core\Definition\Layer\LayerId;
use Cowegis\Core\Definition\Map\MapId;
use Cowegis\Core\Filter\FilterFactory;
use Cowegis\Core\Provider\LayerDataContext;
use Cowegis\Core\Provider\Provider;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use function assert;

final class LayerCallbacksAction
{
    private Provider $provider;

    private FilterFactory $filterFactory;

    private UriFactoryInterface $uriFactory;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        Provider $mapProvider,
        FilterFactory $filterFactory,
        UriFactoryInterface $uriFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->provider        = $mapProvider;
        $this->filterFactory   = $filterFactory;
        $this->uriFactory      = $uriFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(string $mapId, string $layerId, Request $request): Response
    {
        $mapId   = $this->provider->idFormat()->createDefinitionId(MapId::class, $mapId);
        $layerId = $this->provider->idFormat()->createDefinitionId(LayerId::class, $layerId);
        $filter  = $this->filterFactory->createFromUri($this->uriFactory->createUri($request->getUri()));
        $locale  = $request->getLocale();

        assert($mapId instanceof MapId);
        assert($layerId instanceof LayerId);

        $context = LayerDataContext::create($filter, $mapId, $layerId, $locale);
        $this->provider->findLayerData($mapId, $layerId, $context);

        if ($request->query->getBoolean('es5')) {
            $javascript = $context->callbacks()->asEs5Javascript();
        } else {
            $javascript = $context->callbacks()->asJavascript();
        }

        $response = new Response($javascript, 200, ['Content-Type' => 'application/javascript']);
        $this->eventDispatcher->dispatch(new LayerResponseEvent($mapId, $layerId, $response));

        return $response;
    }
}
