<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\Action;

use Cowegis\Core\Definition\Map\MapId;
use Cowegis\Core\Filter\FilterFactory;
use Cowegis\Core\Provider\Provider;
use Cowegis\Core\Provider\ProviderContext;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MapCallbacksAction
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

    public function __invoke(string $mapId, Request $request): Response
    {
        $mapId   = $this->provider->idFormat()->createDefinitionId(MapId::class, $mapId);
        $filter  = $this->filterFactory->createFromUri($this->uriFactory->createUri($request->getUri()));
        $locale  = $request->getLocale();
        $context = ProviderContext::create($filter, $mapId, $locale);

        $this->provider->findMap($mapId, $context);

        return new Response($context->callbacks()->asJavascript(), 200, ['Content-Type' => 'application/javascript']);
    }
}
