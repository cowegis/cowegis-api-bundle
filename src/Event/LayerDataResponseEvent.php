<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\Event;

use Cowegis\Core\Provider\LayerData;
use Symfony\Component\HttpFoundation\Response;

final class LayerDataResponseEvent
{
    private LayerData $layerData;

    private Response $response;

    public function __construct(LayerData $layerData, Response $response)
    {
        $this->layerData = $layerData;
        $this->response  = $response;
    }

    public function layerData(): LayerData
    {
        return $this->layerData;
    }

    public function response(): Response
    {
        return $this->response;
    }
}
