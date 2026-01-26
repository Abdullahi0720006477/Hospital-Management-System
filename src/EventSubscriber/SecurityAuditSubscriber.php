<?php

namespace App\EventSubscriber;

use App\Service\AuditLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class SecurityAuditSubscriber implements EventSubscriberInterface
{
    private $logger;

    public function __construct(AuditLogger $logger)
    {
        $this->logger = $logger;
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $this->logger->log('LOGIN', 'User logged in successfully: ' . $user->getUserIdentifier());
    }

    public function onLogout(LogoutEvent $event): void
    {
        if ($user = $event->getToken()?->getUser()) {
            $this->logger->log('LOGOUT', 'User logged out: ' . $user->getUserIdentifier());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
            LogoutEvent::class => 'onLogout',
        ];
    }
}
