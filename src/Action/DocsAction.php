<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\Action;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class DocsAction
{
    public function __construct(private readonly Environment $twig, private readonly RouterInterface $router)
    {
    }

    public function __invoke(): Response
    {
        return new Response(
            $this->twig->render(
                '@CowegisApi/index.html.twig',
                ['schemaUri' => $this->router->generate('cowegis_api_docs_schema')],
            ),
        );
    }
}
