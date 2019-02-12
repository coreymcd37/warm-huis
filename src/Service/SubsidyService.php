<?php

namespace One\CheckJeHuis\Service;

use One\CheckJeHuis\Entity\Subsidy;
use One\CheckJeHuis\Entity\SubsidyCategory;

class SubsidyService extends AbstractService
{
    /**
     * @return SubsidyCategory[]
     */
    public function getAllSubsidyCategories()
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('Entity:SubsidyCategory');

        return $repo->findAll();
    }

    /**
     * @param int $id
     * @return SubsidyCategory
     */
    public function getSubsidyCategory($id)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('Entity:SubsidyCategory');

        return $repo->find($id);
    }

    /**
     * @return Subsidy[]
     */
    public function getAllSubsidies()
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('Entity:Subsidy');

        return $repo->findAll();
    }

    /**
     * @param int $id
     * @return Subsidy
     */
    public function getSubsidy($id)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('Entity:Subsidy');

        return $repo->find($id);
    }

    /**
     * @param string $slug
     * @return Subsidy[]
     */
    public function getSubsidiesBySlug($slug, $city = null)
    {
        $em = $this->getDoctrine();
        $qb = $em->createQueryBuilder();
        $qb->select('s')
           ->from(Subsidy::class, 's')
           ->andWhere('s.slug = :slug')
           ->setParameter('slug', $slug);
        if ($city) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('s.city', ':city'),
                    $qb->expr()->isNull('s.city')
                )
            );
            $qb->setParameter('city', $city);
        } else {
            $qb->andWhere($qb->expr()->isNull('s.city'));
        }
        $query = $qb->getQuery();

        return $query->getResult();
    }
}
