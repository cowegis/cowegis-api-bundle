<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\Action;

use Cowegis\Bundle\Api\Event\MapResponseEvent;
use Cowegis\Core\Definition\Map\MapId;
use Cowegis\Core\Filter\FilterFactory;
use Cowegis\Core\Provider\Provider;
use Cowegis\Core\Provider\ProviderContext;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use function assert;

final class MapCallbacksAction
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

    public function __invoke(string $mapId, Request $request): Response
    {
        $mapId = $this->provider->idFormat()->createDefinitionId(MapId::class, $mapId);
        assert($mapId instanceof MapId);

        $filter  = $this->filterFactory->createFromUri($this->uriFactory->createUri($request->getUri()));
        $locale  = $request->getLocale();
        $context = ProviderContext::create($filter, $mapId, $locale);

        $definition = $this->provider->findMap($mapId, $context);

        if ($request->query->getBoolean('es5')) {
            $javascript = $context->callbacks()->asEs5Javascript();
        } else {
            $javascript = $context->callbacks()->asJavascript();
        }

        $response = new Response($javascript, 200, ['Content-Type' => 'application/javascript']);
        $this->eventDispatcher->dispatch(new MapResponseEvent($definition, $response));

        return $response;
    }
}
