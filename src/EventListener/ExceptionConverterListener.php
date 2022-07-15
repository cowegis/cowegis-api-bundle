<?php

declare(strict_types=1);

namespace Cowegis\Bundle\Api\EventListener;

use Cowegis\Core\Exception\DataNotFound;
use Cowegis\Core\Exception\InvalidArgument;
use Cowegis\Core\Exception\LayerNotFound;
use Cowegis\Core\Exception\MapNotFound;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function get_class;

final class ExceptionConverterListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        switch (get_class($exception)) {
            case DataNotFound::class:
            case MapNotFound::class:
            case LayerNotFound::class:
                $event->setThrowable(new NotFoundHttpException(null, $exception));
                break;

            case InvalidArgument::class:
                $event->setThrowable(new BadRequestException('', $exception->getCode(), $exception));
        }
    }
}
