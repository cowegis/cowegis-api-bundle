<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use function dump;

final class LocaleListener
{
    public function __invoke(RequestEvent $event) : void
    {
        $request = $event->getRequest();
        if ($request->attributes->get('_cowegis') !== 'api') {
            return;
        }

        if ($request->query->has('_locale')) {
            $request->setLocale($request->query->get('_locale'));
        }
    }
}
