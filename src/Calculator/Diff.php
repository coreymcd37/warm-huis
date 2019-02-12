<?php

namespace One\CheckJeHuis\Calculator;

use One\CheckJeHuis\Entity\ConfigCategory;
use One\CheckJeHuis\Entity\Renewable;

class Diff
{
    /**
     * @var ConfigCategory|Renewable
     */
    protected $subject;

    /**
     * @var float
     */
    protected $gas;

    /**
     * @var float
     */
    protected $elec;

    /**
     * @var float
     */
    protected $oil;

    /**
     * @var float
     */
    protected $co2;

    /**
     * @param $subject
     */
    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param State $state
     */
    public function start(State $state)
    {
        $this->gas  = $state->getGas();
        $this->elec = $state->getElectricity();
        $this->oil = $state->getOil();
        $this->co2  = $state->getCo2();
    }

    /**
     * @param State $state
     */
    public function end(State $state)
    {
        $this->gas  -= $state->getGas();
        $this->elec -= $state->getElectricity();
        $this->oil -= $state->getOil();
        $this->co2  -= $state->getCo2();
    }

    /**
     * @return ConfigCategory|Renewable
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return float
     */
    public function getGas()
    {
        return $this->gas;
    }

    /**
     * @return float
     */
    public function getElec()
    {
        return $this->elec;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->getGasOrOil() + $this->getElec();
    }

    /**
     * @return float
     */
    public function getCo2()
    {
        return $this->co2;
    }

    /**
     * @return float
     */
    public function getOil()
    {
        return $this->oil;
    }

    public function getGasOrOil()
    {
        if ($this->getOil()) {
            return $this->getOil();
        }

        return $this->getGas();
    }
}
