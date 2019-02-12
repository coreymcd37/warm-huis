<?php

namespace One\CheckJeHuis\Calculator;

use One\CheckJeHuis\Entity\BuildCost;
use One\CheckJeHuis\Entity\ConfigCategory;
use One\CheckJeHuis\Entity\House;
use One\CheckJeHuis\Entity\Renewable;
use One\CheckJeHuis\Entity\Subsidy;

class SubsidyCalculator
{
    /**
     * @var House
     */
    protected $house;

    /**
     * @var array|Subsidy[]
     */
    protected $windRoofSubsidies;

    /**
     * @var BuildCost
     */
    protected $windRoofBuildCost;

    /**
     * @var array|Subsidy[]
     */
    protected $solarHeaterSubsidies;

    /**
     * @var BuildCost
     */
    protected $solarHeaterBuildCost;

    /**
     * @var array
     */
    protected $categories = array();

    /**
     * @var array
     */
    protected $renewables = array();

    /**
     * @var array|float[]
     */
    protected $windRoofPrice = 0;

    /**
     * @var float
     */
    protected $totalPrice = 0;

    /**
     * The max amount of roof insulation subsidies Stad Gent will hand out
     *
     * @var float
     */
    protected $subsidyCeilingGentRoof = 0;

    /**
     * @var Subsidy[]
     */
    protected $subsidies;

    public function __construct(House $house)
    {
        $this->house = $house;
    }

    /**
     * @param array|\One\CheckJeHuis\Entity\Subsidy[] $windRoofSubsidies
     * @return $this
     */
    public function setWindRoofSubsidies($windRoofSubsidies)
    {
        $this->windRoofSubsidies = $windRoofSubsidies;
        return $this;
    }

    /**
     * @param BuildCost $windRoofBuildCost
     * @return $this
     */
    public function setWindRoofBuildCost($windRoofBuildCost)
    {
        $this->windRoofBuildCost = $windRoofBuildCost;
        return $this;
    }

    /**
     * @param array|\One\CheckJeHuis\Entity\Subsidy[] $solarHeaterSubsidies
     * @return $this
     */
    public function setSolarHeaterSubsidies($solarHeaterSubsidies)
    {
        $this->solarHeaterSubsidies = $solarHeaterSubsidies;
        return $this;
    }

    /**
     * @param BuildCost $solarHeaterBuildCost
     * @return $this
     */
    public function setSolarHeaterBuildCost($solarHeaterBuildCost)
    {
        $this->solarHeaterBuildCost = $solarHeaterBuildCost;
        return $this;
    }

    /**
     * @param float $subsidyCeilingGentRoof
     * @return $this
     */
    public function setSubsidyCeilingGentRoof($subsidyCeilingGentRoof)
    {
        $this->subsidyCeilingGentRoof = $subsidyCeilingGentRoof;
        return $this;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return array
     */
    public function getRenewables()
    {
        return $this->renewables;
    }

    /**
     * @return array|\float[]
     */
    public function getWindRoofPrice()
    {
        return $this->windRoofPrice;
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    public function calculate()
    {
        $roofCat = null;

        // configs
        foreach ($this->house->getUpgradeConfigs() as $c) {
            // renters only get subsidies for roof insulation
            if ($this->house->getOwnership() === House::OWNERSHIP_RENTER
                && $c->getCategory()->getSlug() !== ConfigCategory::CAT_ROOF) {
                continue;
            }

            foreach ($c->getSubsidies() as $s) {
                if ($s->getCity() !== null && $s->getCity() != $this->house->getCity()) {
                    continue;
                }
                $price = $s->getPrice($this->house, $c->getCost(), array('roof-type' => House::ROOF_TYPE_INCLINED));

                $this->totalPrice += $price;
                $this->add($this->categories[$c->getCategory()->getSlug()][$s->getCategory()->getId()], $price);
            }
        }

        // add flat part of mixed roof
        if ($this->house->getRoofType() === House::ROOF_TYPE_MIXED && $this->house->getExtraUpgradeRoof()) {
            $c = $this->house->getExtraUpgradeRoof();
            foreach ($c->getSubsidies() as $s) {
                if ($s->getCity() !== null && $s->getCity() != $this->house->getCity()) {
                    continue;
                }
                $price = $s->getPrice($this->house, $c->getCost(), array('roof-type' => House::ROOF_TYPE_FLAT));

                $this->totalPrice += $price;
                $this->add($this->categories[$c->getCategory()->getSlug()][$s->getCategory()->getId()], $price);
            }
        }

        // add subsidy for placing windroof
        if (!$this->house->hasWindRoof() && $this->house->getPlaceWindroof() && $this->house->getRoofType() !== House::ROOF_TYPE_FLAT) {
            foreach ($this->windRoofSubsidies as $s) {
                $price = $s->getPrice($this->house, $this->windRoofBuildCost, array('roof-type' => House::ROOF_TYPE_INCLINED));

                $this->totalPrice += $price;
                $this->add($this->categories[ConfigCategory::CAT_WIND_ROOF][$s->getCategory()->getId()], $price);
            }
        }

        // nothing more for renters
        if ($this->house->getOwnership() === House::OWNERSHIP_RENTER) {
            return;
        }

        // add subsidy for solar boiler
        foreach ($this->house->getUpgradeRenewables() as $r) {
            if ($r->getSlug() === Renewable::RENEWABLE_SOLAR_WATER_HEATER) {
                foreach ($this->solarHeaterSubsidies as $s) {
                    $price = $s->getPrice($this->house, $this->solarHeaterBuildCost);
                    $this->totalPrice += $price;
                    $this->add($this->renewables[$r->getSlug()][$s->getCategory()->getId()], $price);
                }
            }
        }
    }

    protected function add(&$var, $val)
    {
        if (!isset($var)) {
            $var = $val;
        } else {
            $var += $val;
        }
    }
}
