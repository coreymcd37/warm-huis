<?php

namespace One\CheckJeHuis\Repository;

use Doctrine\ORM\EntityRepository;
use One\CheckJeHuis\Entity\Renewable;

class RenewableRepository extends EntityRepository
{
    /**
     * @return Renewable[]
     */
    public function getAll()
    {
        return parent::findAll();
    }

    /**
     * @param int $id
     * @return Renewable
     */
    public function getRenewable($id)
    {
        return parent::find($id);
    }

    /**
     * @param string $slug
     * @return Renewable
     */
    public function getRenewableBySlug($slug)
    {
        return parent::findOneBy(array("slug" => $slug));
    }

    public function add(Renewable $renewable)
    {
        $this->_em->persist($renewable);
    }
}
