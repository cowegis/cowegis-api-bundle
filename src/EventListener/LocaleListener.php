<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use function is_string;

final class LocaleListener
{
    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->attributes->get('_cowegis') !== 'api') {
            return;
        }

        if (! $request->query->has('_locale')) {
            return;
        }

        $locale = $request->query->get('_locale');
        if (!is_string($locale)) {
            return;
        }

        $request->setLocale($locale);
    }
}
