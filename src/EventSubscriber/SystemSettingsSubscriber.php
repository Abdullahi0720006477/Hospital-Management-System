<?php

namespace App\EventSubscriber;

use App\Repository\SystemSettingsRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class SystemSettingsSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $settingsRepository;

    public function __construct(Environment $twig, SystemSettingsRepository $settingsRepository)
    {
        $this->twig = $twig;
        $this->settingsRepository = $settingsRepository;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        // Add the global 'system_settings' variable to Twig
        $settings = $this->settingsRepository->getSettings();
        $this->twig->addGlobal('system_settings', $settings);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
