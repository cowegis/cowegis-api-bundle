<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\Event;

use Cowegis\Core\Definition\Map\Map;
use Symfony\Component\HttpFoundation\Response;

final class MapResponseEvent
{
    public function __construct(private readonly Map $definition, private readonly Response $response)
    {
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
