<?php

namespace One\CheckJeHuis\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use One\CheckJeHuis\Entity\AuditLog;
use One\CheckJeHuis\Entity\BuildCost;
use One\CheckJeHuis\Entity\City;
use One\CheckJeHuis\Entity\Config;
use One\CheckJeHuis\Entity\ConfigTransformation;
use One\CheckJeHuis\Entity\Content;
use One\CheckJeHuis\Entity\DefaultEnergy;
use One\CheckJeHuis\Entity\DefaultRoof;
use One\CheckJeHuis\Entity\DefaultSurface;
use One\CheckJeHuis\Entity\Renewable;
use One\CheckJeHuis\Entity\Subsidy;
use One\CheckJeHuis\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use One\CheckJeHuis\Entity\Parameter;

class EntityListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    private $trackedEntities = [
        BuildCost::class,
        City::class,
        Config::class,
        ConfigTransformation::class,
        Content::class,
        DefaultEnergy::class,
        DefaultRoof::class,
        DefaultSurface::class,
        Parameter::class,
        Renewable::class,
        Subsidy::class,
        User::class
    ];

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $entityManager = $eventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();
        $updatedEntities = $unitOfWork->getScheduledEntityUpdates();

        foreach ($updatedEntities as $updatedEntity) {
            $entityName = $entityManager->getClassMetadata(get_class($updatedEntity))->name;
            $metaData = $entityManager->getClassMetadata($entityName);
            if (!in_array($entityName, $this->trackedEntities, true)) {
                continue;
            }

            $changeset = $unitOfWork->getEntityChangeSet($updatedEntity);
            if (!is_array($changeset)) {
                continue;
            }

            foreach ($changeset as $property => $change) {
                $previousValueForField = array_key_exists(0, $change) ? $change[0] : null;
                $newValueForField = array_key_exists(1, $change) ? $change[1] : null;

                if ($previousValueForField == $newValueForField) {
                    continue 2;
                }
            }

            $username = $this->tokenStorage->getToken()?$this->tokenStorage->getToken()->getUsername():'';
            $auditLog = AuditLog::createEntry(
                $username,
                $entityName,
                call_user_func(array($updatedEntity, 'get'. ucfirst($metaData->getSingleIdentifierFieldName()))),
                $changeset
            );
            $entityManager->persist($auditLog);

            $metaData = $entityManager->getClassMetadata(AuditLog::class);
            $unitOfWork->computeChangeSet($metaData, $auditLog);
        }
    }
}
