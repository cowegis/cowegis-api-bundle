<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\Event;

use Cowegis\Core\Definition\Map\Map;
use Symfony\Component\HttpFoundation\Response;

final class MapResponseEvent
{
    private Map $definition;

    private Response $response;

    public function __construct(Map $definition, Response $response)
    {
        $this->definition = $definition;
        $this->response   = $response;
    }

    public function definition(): Map
    {
        return $this->definition;
    }

    public function response(): Response
    {
        return $this->response;
    }
}
