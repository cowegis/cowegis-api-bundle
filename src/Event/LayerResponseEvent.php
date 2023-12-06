<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\Event;

use Cowegis\Core\Definition\Layer\LayerId;
use Cowegis\Core\Definition\Map\MapId;
use Symfony\Component\HttpFoundation\Response;

final class LayerResponseEvent
{
    public function __construct(
        private readonly MapId $mapId,
        private readonly LayerId $layerId,
        private readonly Response $response,
    ) {
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
