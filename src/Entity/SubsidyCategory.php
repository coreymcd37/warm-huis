<?php

namespace One\CheckJeHuis\Entity;

use Doctrine\Common\Collections\Criteria;

class SubsidyCategory
{
    protected $id;

    protected $label;

    protected $subsidies;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return Subsidy[]
     */
    public function getSubsidies()
    {
        return $this->subsidies;
    }

    /**
     * @return Subsidy[]
     */
    public function getSubsidiesForCity($city)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('city', $city))
            ->orWhere(Criteria::expr()->isNull('city'))
        ;

        return $this->subsidies->matching($criteria);
    }

    /**
     * @param Subsidy[] $subsidies
     * @return $this
     */
    public function setSubsidies($subsidies)
    {
        $this->subsidies = $subsidies;
        return $this;
    }
}
