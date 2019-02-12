<?php

namespace One\CheckJeHuis\Repository;

use Doctrine\ORM\EntityRepository;
use One\CheckJeHuis\Entity\City;
use One\CheckJeHuis\Entity\User;

class UserRepository extends EntityRepository
{
    public function add($user)
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
    public function removeUsersFromCity(City $city)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete('Entity:User', 'u');
        $qb->where('u.city = :city');
        $qb->setParameter('city', $city);
        $query = $qb->getQuery();
        $query->execute();
    }

    public function remove(User $user)
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }
}
