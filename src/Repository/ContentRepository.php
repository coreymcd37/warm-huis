<?php

namespace One\CheckJeHuis\Repository;

use Doctrine\ORM\EntityRepository;
use One\CheckJeHuis\Entity\City;
use One\CheckJeHuis\Entity\Content;

class ContentRepository extends EntityRepository
{
    /**
     * @return Content[]
     */
    public function getAllContent(City $city = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('c')
            ->from(Content::class, 'c')
            ->where('c.city IS NULL');
        if ($city) {
            $qb->orWhere($qb->expr()->eq('c.city', $city));
        }

        return parent::findByCity($city);
    }

    public function getContent(int $id)
    {
        return parent::find($id);
    }

    public function getContentBySlug(string $slug, $city = null)
    {
        $params = ['slug' => $slug];
        if ($city) {
            $params['city'] = $city;
        }
        return parent::findOneBy($params);
    }

    public function add(Content $content, $flush = true)
    {
        $this->_em->persist($content);
        if (true === $flush) {
            $this->_em->flush();
        }
    }

    public function removeContentFromCity(City $city)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete('Entity:Content', 'c');
        $qb->where('c.city = :city');
        $qb->setParameter('city', $city);
        $query = $qb->getQuery();
        $query->execute();
    }
}
