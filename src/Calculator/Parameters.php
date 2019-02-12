<?php

namespace One\CheckJeHuis\Calculator;

class Parameters
{
    /**
     * Price of gas
     *
     * @var float
     */
    protected $priceGas;

    /**
     * Price of electricity
     *
     * @var float
     */
    protected $priceElec;

    /**
     * Price of oil
     *
     * @var float
     */
    protected $priceOil;

    /**
     * How much CO2 one KwH of energy produces
     *
     * @var float
     */
    protected $co2PerKwh;

    /**
     * @var float
     */
    protected $energyLoanThreshold;

    /**
     * @var float
     */
    protected $energyLoanInterestRate;

    /**
     * @var int
     */
    protected $energyLoanDuration;

    /**
     * @var float
     */
    protected $mortgageInterestRate;

    /**
     * @var float
     */
    protected $mortgageDuration;

    /**
     * @return float
     */
    public function getPriceGas()
    {
        return $this->priceGas;
    }

    /**
     * @param float $priceGas
     * @return $this
     */
    public function setPriceGas($priceGas)
    {
        $this->priceGas = $priceGas;
        return $this;
    }

    /**
     * @return float
     */
    public function getPriceElec()
    {
        return $this->priceElec;
    }

    /**
     * @param float $priceElec
     * @return $this
     */
    public function setPriceElec($priceElec)
    {
        $this->priceElec = $priceElec;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriceOil()
    {
        return $this->priceOil;
    }

    /**
     * @param mixed $priceOil
     */
    public function setPriceOil($priceOil)
    {
        $this->priceOil = $priceOil;
    }

    /**
     * @return float
     */
    public function getCo2PerKwh()
    {
        return $this->co2PerKwh;
    }

    /**
     * @param float $co2PerKwh
     * @return $this
     */
    public function setCo2PerKwh($co2PerKwh)
    {
        $this->co2PerKwh = $co2PerKwh;
        return $this;
    }

    public function setEnergyLoanThreshold($energyLoanThreshold)
    {
        $this->energyLoanThreshold = $energyLoanThreshold;
    }

    /**
     * @return float
     */
    public function getEnergyLoanThreshold()
    {
        return $this->energyLoanThreshold;
    }

    /**
     * @return float
     */
    public function getEnergyLoanInterestRate()
    {
        return $this->energyLoanInterestRate;
    }

    /**
     * @param float $energyLoanInterestRate
     */
    public function setEnergyLoanInterestRate($energyLoanInterestRate)
    {
        $this->energyLoanInterestRate = $energyLoanInterestRate;
    }

    /**
     * @return int
     */
    public function getEnergyLoanDuration()
    {
        return $this->energyLoanDuration;
    }

    /**
     * @param int $energyLoanDuration
     */
    public function setEnergyLoanDuration($energyLoanDuration)
    {
        $this->energyLoanDuration = $energyLoanDuration;
    }

    /**
     * @return float
     */
    public function getMortgageInterestRate()
    {
        return $this->mortgageInterestRate;
    }

    /**
     * @param float $mortgageInterestRate
     */
    public function setMortgageInterestRate($mortgageInterestRate)
    {
        $this->mortgageInterestRate = $mortgageInterestRate;
    }

    /**
     * @return mixed
     */
    public function getMortgageDuration()
    {
        return $this->mortgageDuration;
    }

    /**
     * @param mixed $mortgageDuration
     */
    public function setMortgageDuration($mortgageDuration)
    {
        $this->mortgageDuration = $mortgageDuration;
    }
}
