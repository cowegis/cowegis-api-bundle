<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\Action;

use Cowegis\Bundle\Api\Event\MapResponseEvent;
use Cowegis\Core\Definition\Asset\Asset;
use Cowegis\Core\Definition\Map\MapId;
use Cowegis\Core\Filter\FilterFactory;
use Cowegis\Core\Provider\Provider;
use Cowegis\Core\Provider\ProviderContext;
use Cowegis\Core\Serializer\Serializer;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use function assert;
use function count;

final class MapAction
{
    private Provider $provider;

    private Serializer $serializer;

    private FilterFactory $filterFactory;

    private UriFactoryInterface $uriFactory;

    private RouterInterface $router;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        Provider $provider,
        FilterFactory $filterFactory,
        Serializer $serializer,
        UriFactoryInterface $uriFactory,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->provider        = $provider;
        $this->serializer      = $serializer;
        $this->filterFactory   = $filterFactory;
        $this->uriFactory      = $uriFactory;
        $this->router          = $router;
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

        if (count($context->callbacks()) > 0) {
            $callbacksUrl = $this->router->generate(
                'cowegis_api_js_map_callbacks',
                ['mapId' => $mapId->value(), 'es5' => $request->query->getBoolean('es5')]
            );
            $context->assets()->add(Asset::CALLBACKS($context->callbacks()->identifier(), $callbacksUrl));
        }

        // TODO: Apply cache strategy defined in the map definition
        $data = [
            'map'    => $this->serializer->serialize($definition),
            'assets' => $this->serializer->serialize($context->assets()->toArray()),
        ];

        $response = new JsonResponse($data);
        $this->eventDispatcher->dispatch(new MapResponseEvent($definition, $response));

        return $response;
    }
}
