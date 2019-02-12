<?php

namespace One\CheckJeHuis\Repository;

use Doctrine\ORM\EntityRepository;
use One\CheckJeHuis\Entity\DefaultEnergy;

class DefaultEnergyRepository extends EntityRepository
{
    /**
     * @param array $filter
     * @return DefaultEnergy[]
     */
    public function getAllEnergy(array $filter = array())
    {
        return parent::findBy($this->getFilterCriteria($filter, array('building-size', 'building-type', 'building-year')));
    }

    public function getEnergyById(int $id)
    {
        return parent::find($id);
    }

    private function getFilterCriteria(array $filter, array $fields)
    {
        $criteria = array();

        if (in_array('building-type', $fields) && isset($filter['building-type']) && !empty($filter['building-type'])) {
            $criteria['type'] = $filter['building-type'];
        }
        if (in_array('building-size', $fields) && isset($filter['building-size']) && !empty($filter['building-size'])) {
            $criteria['size'] = $filter['building-size'];
        }
        if (in_array('building-year', $fields) && isset($filter['building-year']) && !empty($filter['building-year'])) {
            $criteria['maxYear'] = $filter['building-year'];
        }
        if (in_array('roof-type', $fields) && isset($filter['roof-type']) && !empty($filter['roof-type'])) {
            $criteria['inclined'] = $filter['roof-type'];
        }

        return $criteria;
    }
}
