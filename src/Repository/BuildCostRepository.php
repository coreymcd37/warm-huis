<?php

namespace One\CheckJeHuis\Repository;

use Doctrine\ORM\EntityRepository;
use One\CheckJeHuis\Entity\BuildCost;

class BuildCostRepository extends EntityRepository
{
    /**
     * @return BuildCost[]
     */
    public function getAll()
    {
        return parent::findBy(array(), array('ordering' => 'ASC'));
    }

    public function getCost(int $id)
    {
        return parent::find($id);
    }

    public function getCostBySlug(string $slug)
    {
        return parent::findOneBy(array('slug' => $slug));
    }
}
