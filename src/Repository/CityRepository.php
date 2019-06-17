<?php

namespace One\CheckJeHuis\Repository;

use Doctrine\ORM\EntityRepository;
use One\CheckJeHuis\Entity\City;
use Symfony\Component\HttpFoundation\Request;

class CityRepository extends EntityRepository
{
    public function add(City $city)
    {
        $this->getEntityManager()->persist($city);
        $this->getEntityManager()->flush();
    }

    public function remove(City $city)
    {
        $this->getEntityManager()->remove($city);
        $this->getEntityManager()->flush();
    }

    public function determineCity(Request $request)
    {
        if ($request->query->has('city')) {
            $cityId = $request->query->get('city');
            $city = $this->find($cityId);

            if ($city instanceof City) {
                return $city;
            }
        }

        $url = $request->getHost();
        $city = $this->findOneBy(['url' => $url]);
        if ($city instanceof City) {
            return $city;
        }
        $url = ltrim($request->getPathInfo(), '/');
        $city = $this->findOneBy(['url' => $url]);

        return $city;
    }

    public function findAllForSelection($onlyVisible = false)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('c')
            ->from('Entity:City', 'c')
            ->orderBy('c.name');
        if ($onlyVisible) {
            $qb->where('c.showInDropdown = 1');
        }

        return $qb->getQuery()->getResult();
    }
}
