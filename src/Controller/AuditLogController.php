<?php

namespace App\Controller;

use App\Repository\AuditLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AuditLogController extends AbstractController
{
    #[Route('/audit-logs', name: 'app_admin_audit_logs')]
    public function index(AuditLogRepository $auditLogRepository): Response
    {
        return $this->render('audit_log/index.html.twig', [
            'logs' => $auditLogRepository->findBy([], ['createdAt' => 'DESC'], 50),
        ]);
    }
}
