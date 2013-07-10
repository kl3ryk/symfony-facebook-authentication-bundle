<?php

namespace Laelaps\Bundle\FacebookAuthentication\EventListener;

use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationExceptionListener implements EventSubscriberInterface
{
    /**
     * @param \Exception $exception
     * @return bool
     */
    public function isExceptionSupported(Exception $exception)
    {
        return $exception instanceof AuthenticationException;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!$this->isExceptionSupported($exception)) {
            return;
        }

        var_dump(get_class($event));
        die;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }
}
