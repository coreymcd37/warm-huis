<?php

namespace One\CheckJeHuis\Repository;

use Doctrine\ORM\EntityRepository;
use One\CheckJeHuis\Entity\City;
use One\CheckJeHuis\Entity\Subsidy;

class SubsidyRepository extends EntityRepository
{
    public function add(Subsidy $subsidy)
    {
        $this->_em->persist($subsidy);
        $this->_em->flush();
    }

    public function getSubsidiesFromCity(City $city)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s');
        $qb->from(Subsidy::class, 's');
        $qb->where('s.city = :city');
        $qb->setParameter('city', $city);
        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function removeSubsidy($subSidy)
    {
        $em = $this->getEntityManager();
        $em->remove($subSidy);
        $em->flush();
    }
}
