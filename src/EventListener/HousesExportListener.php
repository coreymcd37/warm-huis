<?php

namespace One\CheckJeHuis\EventListener;

use Doctrine\ORM\EntityManager;
use One\CheckJeHuis\Entity\AuditLog;
use One\CheckJeHuis\EventListener\Event\HousesExportEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HousesExportListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    private $entityManager;


    public function __construct(TokenStorageInterface $tokenStorage, EntityManager $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    public function onHousesExport(HousesExportEvent $event)
    {
        $auditLog = AuditLog::createEntry(
            $this->tokenStorage->getToken()->getUsername(),
            'Houses Export',
            '',
            $event->getFilter()
        );
        $this->entityManager->persist($auditLog);
        $this->entityManager->flush();
    }
}
