<?php

namespace App\Service;

use App\Entity\AuditLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class AuditLogger
{
    private $entityManager;
    private $security;
    private $requestStack;

    public function __construct(EntityManagerInterface $entityManager, Security $security, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function log(string $action, string $description, ?string $entityName = null, ?int $entityId = null): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $user = $this->security->getUser();

        $log = new AuditLog();
        $log->setAction($action);
        $log->setDescription($description);
        $log->setEntityName($entityName);
        $log->setEntityId($entityId);
        $log->setIpAddress($request ? $request->getClientIp() : '127.0.0.1');
        $log->setPerformedBy($user ? $user->getUserIdentifier() : 'System/Guest');

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
