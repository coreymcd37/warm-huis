<?php

namespace One\CheckJeHuis\Repository;

use Doctrine\ORM\EntityRepository;
use One\CheckJeHuis\Entity\AuditLog;
use One\CheckJeHuis\Entity\City;

class AuditLogRepository extends EntityRepository
{
    public function find($id)
    {
        return parent::find($id);
    }

    public function add(City $city)
    {
        $this->getEntityManager()->persist($city);
        $this->getEntityManager()->flush();
    }

    public function getFindAllQuery()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('a')->from(AuditLog::class, 'a')->orderBy('a.createdAt', 'DESC');

        return $qb->getQuery();
    }
}
