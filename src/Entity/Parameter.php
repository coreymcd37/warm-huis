<?php

namespace One\CheckJeHuis\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Parameter
{
    const PARAM_PRICE_ELEC          = 'price_electricity';
    const PARAM_PRICE_GAS           = 'price_gas';
    const PARAM_PRICE_OIL           = 'price_oil';
    const PARAM_SOLAR_SURFACE       = 'solar_panel_surface';
    const PARAM_SOLAR_PANEL_SURFACE_SINGLE = 'solar_panel_surface_single';
    const PARAM_SOLAR_PEAK_SINGLE = 'solar_panel_peak_single';
    const PARAM_SOLAR_YIELD = 'solar_panel_yield';
    const PARAM_CO2_KWH             = 'co2_kwh';
    const PARAM_SUBSIDY_GENT_ROOF   = 'subsidy_ceiling_roof_gent';
    const PARAM_ENERGY_LOAN_THRESHOLD = 'energy_loan_threshold';
    const PARAM_ENERGY_LOAN_INTEREST_RATE = 'energy_loan_interest_rate';
    const PARAM_ENERGY_LOAN_DURATION = 'energy_loan_duration';
    const PARAM_MORTGAGE_INTEREST_RATE = 'mortgage_interest_rate';
    const PARAM_MORTGAGE_DURATION = 'mortgage_duration';

    protected $id;

    protected $slug;

    protected $label;

    /**
     * @Assert\Type(type="numeric", message = "dit is geen geldige numerieke waarde")
     */
    protected $value;

    protected $unit;

    protected $city;

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
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     * @return $this
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }
}
