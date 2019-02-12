<?php

namespace One\CheckJeHuis\Controller\Admin;

use Knp\Component\Pager\Paginator;
use One\CheckJeHuis\Entity\AuditLog;
use One\CheckJeHuis\Repository\AuditLogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service="one.check_je_huis.controller.admin.audit_log_controller")
 */
class AuditLogController extends Controller
{
    /**
     * @var AuditLogRepository
     */
    private $auditLogRepository;

    /**
     * @var Paginator
     */
    private $paginator;

    public function __construct(AuditLogRepository $auditLogRepository, Paginator $paginator)
    {
        $this->auditLogRepository = $auditLogRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $query = $this->auditLogRepository->getFindAllQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1),
            100
        );

        return $this->render(':Admin/AuditLog:list.html.twig', [
            'logs' => $pagination,
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function viewAction(AuditLog $log)
    {
        return $this->render(':Admin/AuditLog:view.html.twig', [
            'log' => $log,
        ]);
    }
}