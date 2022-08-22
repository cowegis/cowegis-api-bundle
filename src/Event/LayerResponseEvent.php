<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\Event;

use Cowegis\Core\Definition\Layer\LayerId;
use Cowegis\Core\Definition\Map\MapId;
use Symfony\Component\HttpFoundation\Response;

final class LayerResponseEvent
{
    private MapId $mapId;

    private LayerId $layerId;

    private Response $response;

    public function __construct(MapId $mapId, LayerId $layerId, Response $response)
    {
        $this->mapId    = $mapId;
        $this->layerId  = $layerId;
        $this->response = $response;
    }

    public function response(): Response
    {
        return $this->response;
    }

    public function mapId(): MapId
    {
        return $this->mapId;
    }

    public function layerId(): LayerId
    {
        return $this->layerId;
    }
}
