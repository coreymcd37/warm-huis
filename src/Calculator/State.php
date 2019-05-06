<?php

namespace One\CheckJeHuis\Calculator;

use One\CheckJeHuis\Entity\ConfigCategory;
use One\CheckJeHuis\Entity\House;

class State
{
    /**
     * @var float
     */
    protected $gas = 0;

    /**
     * @var float
     */
    protected $electricity = 0;

    /**
     * @var float
     */
    protected $oil = 0;

    /**
     * @var float
     */
    protected $co2 = 0;

    /**
     * @var float
     */
    protected $start = 0;

    /**
     * @var float
     */
    protected $subtotal = 0;

    /**
     * @var float
     */
    protected $baseElectricity = 0;

    /**
     * @var float
     */
    protected $nonHeatingElectricity = 0;

    /**
     * @var float
     */
    protected $electricHeatingEnergy = 0;

    /**
     * @var bool
     */
    protected $isHeatingElectric = false;

    /**
     * @var bool
     */
    protected $forceElectricity = false;

    /**
     * @var House
     */
    protected $house;


    public function __construct($gas, $electricity, $oil, $nonHeatingElectricity, $isHeatingElectric, $house)
    {
        $this->setGas($gas);
        $this->setElectricity($electricity);
        $this->setOil($oil);
        $this->setBaseElectricity($electricity);
        $this->setIsHeatingElectric($isHeatingElectric);
        $this->setNonHeatingElectricity($nonHeatingElectricity);
        $this->house = $house;

        $this->init();
    }

    public function init()
    {
        if ($this->isHeatingElectric()) {
            $this->start = $this->electricity - $this->nonHeatingElectricity;
        } else {
            $this->start = $this->getGasOrOil();
        }

        $this->co2 = 0;
        $this->subtotal = $this->start;

        return $this;
    }

    public static function createFormHouse(House $house)
    {
        $isElectric = false;

        if ($house->hasCustomEnergy()) {
            $gas = $house->getConsumptionGas(false);
            $elec = $house->getConsumptionElec(false);
            $oil = $house->getConsumptionOil(false);
        } else {
            $gas = $house->getDefaultEnergy()->getGas();
            $elec = $house->getDefaultEnergy()->getElectricity($house->hasElectricHeating());
            $oil = null;
        }

        // we ignore the gas and oil if heating is electric
        if ($house->hasElectricHeating()) {
            $isElectric = true;
            $gas = 0;
            $oil = 0;
        }

        return new self($gas, $elec, $oil, $house->getDefaultEnergy()->getElectricity(), $isElectric, $house);
    }

    /**
     * @return float
     */
    public function getGas()
    {
        return $this->gas;
    }

    /**
     * @param float $gas
     * @return $this
     */
    public function setGas($gas)
    {
        $this->gas = $gas;
        return $this;
    }

    /**
     * @return float
     */
    public function getElectricity()
    {
        return $this->electricity;
    }

    /**
     * @param float $electricity
     * @return $this
     */
    public function setElectricity($electricity)
    {
        $this->electricity = $electricity;
        return $this;
    }

    /**
     * @return float
     */
    public function getOil()
    {
        return $this->oil;
    }

    /**
     * @param float $oil
     * @return $this
     */
    public function setOil($oil)
    {
        $this->oil = $oil;
        return $this;
    }

    /**
     * @return float
     */
    public function getGasOrOil()
    {
        if ($this->getOil()) {
            return 10 * $this->getOil();
        }

        return $this->getGas();
    }

    /**
     * @return float
     */
    public function getCo2()
    {
        return $this->co2 * -1;
    }

    /**
     * @param float $co2
     * @return $this
     */
    public function setCo2($co2)
    {
        $this->co2 = $co2;
        return $this;
    }

    /**
     * @return float
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param float $start
     * @return $this
     */
    public function setStart($start)
    {
        $this->start = $start;
        $this->subtotal = $start;

        return $this;
    }

    /**
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @param float $subtotal
     * @return $this
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
        return $this;
    }

    /**
     * @return float
     */
    public function getBaseElectricity()
    {
        return $this->baseElectricity;
    }

    /**
     * @param float $baseElectricity
     * @return $this
     */
    public function setBaseElectricity($baseElectricity)
    {
        $this->baseElectricity = $baseElectricity;
        return $this;
    }

    /**
     * @return float
     */
    public function getNonHeatingElectricity()
    {
        return $this->nonHeatingElectricity;
    }

    /**
     * @param float $nonHeatingElectricity
     * @return $this
     */
    public function setNonHeatingElectricity($nonHeatingElectricity)
    {
        $this->nonHeatingElectricity = $nonHeatingElectricity;
        return $this;
    }

    /**
     * @return float
     */
    public function getElectricHeatingEnergy()
    {
        return $this->electricHeatingEnergy;
    }

    /**
     * @param float $electricHeatingEnergy
     * @return $this
     */
    public function setElectricHeatingEnergy($electricHeatingEnergy)
    {
        $this->electricHeatingEnergy = $electricHeatingEnergy;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isHeatingElectric()
    {
        return $this->isHeatingElectric;
    }

    /**
     * @param boolean $isHeatingElectric
     * @return $this
     */
    public function setIsHeatingElectric($isHeatingElectric)
    {
        $this->isHeatingElectric = $isHeatingElectric;
        return $this;
    }

    /**
     * @return boolean
     */
    public function forceElectricity()
    {
        return $this->forceElectricity;
    }

    /**
     * @param boolean $forceElectricity
     * @return $this
     */
    public function setForceElectricity()
    {
        if (!$this->forceElectricity) {
            $this->forceElectricity = true;
            $this->electricity += $this->gas;
            $this->gas = 0;
            $this->oil = 0;
        }


        return $this;
    }

    /**
     * Returns the base figure to use when calculating energy differences for a certain category
     *
     * @param ConfigCategory $category
     * @return float
     */
    public function getCalculationBaseFormCategory(ConfigCategory $category)
    {
        if (!$category->isFromActual()) {
            return $this->getStart();
        }

        if ($this->isHeatingElectric() || $this->forceElectricity()) {
            return $this->getSubtotal();
        }

        return $this->getGasOrOil();
    }

    /**
     * Subtracts energy from the correct type
     *
     * @param float $amount
     * @param bool $fromActual
     * @param bool $forceElec
     * @return $this
     */
    public function subtractEnergy($amount, $fromActual, $forceElec = false)
    {
        if (($forceElec || $this->forceElectricity) || $this->isHeatingElectric()) {
            $this->electricity -= $amount;
        } else {
            if ($this->oil) {
                $this->oil -= $amount / 10;
            } else {
                $this->gas -= $amount;
            }
        }

        if (!$fromActual) {
            $this->subtotal -= $amount;
        }

        /*
         * cant go below zero
         */

        if ($this->electricity < 0) {
            $this->electricity = 0;
        }
        if ($this->gas < 0) {
            $this->gas = 0;
        }
        if ($this->subtotal < 0) {
            $this->subtotal = 0;
        }

        return $this;
    }

    /**
     * Subtracts CO2
     *
     * @param float $amount
     * @return $this
     */
    public function subtractCo2($amount)
    {
        $this->co2 -= $amount;

        return $this;
    }

    public function __clone()
    {
        $this->init();
    }

    public function getOilForSummary()
    {
        if ($this->house->getConsumptionOil(false) == 0 && $this->house->getConsumptionGas(false) == 0) {
            return $this->house->getDefaultEnergy()->getOil();
        }

        return $this->house->getConsumptionOil(false);
    }

    public function getGasForSummary()
    {
        if ($this->house->getConsumptionOil(false) == 0 && $this->house->getConsumptionGas(false) == 0) {
            return $this->house->getDefaultEnergy()->getGas();
        }

        return $this->house->getConsumptionGas(false);
    }
}
