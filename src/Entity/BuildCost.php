<?php

namespace One\CheckJeHuis\Entity;

use One\CheckJeHuis\Service\SolarPanelCalculatorService;

class BuildCost
{
    const COST_WINDROOF             = 'roof_wind';
    const COST_SOLAR_WATER_HEATER   = Renewable::RENEWABLE_SOLAR_WATER_HEATER;
    const COST_SOLAR_PANELS         = Renewable::RENEWABLE_SOLAR_PANELS;

    protected $id;

    protected $ordering;

    protected $slug;

    protected $label;

    protected $value;

    protected $unit;

    protected $relatedConfigs;

    protected $relatedRenewables;

    private $solarPanelCalculatorService;

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
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param mixed $unit
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
     * @param House $house
     * @param Config $config
     * @param array $options
     * @return float
     */
    public function getPrice(House $house, array $options = array())
    {
        $amount = 1;

        switch ($this->getSlug()) {
            case 'roof_18':
            case 'roof_24':
            case 'roof_30':
            case 'roof_wind':
                $amount = $house->getSurfaceRoof();
                if ($house->getRoofType() == House::ROOF_TYPE_MIXED) {
                    if ($options['roof-type'] == House::ROOF_TYPE_FLAT) {
                        $amount = $house->getSurfaceRoofExtra();
                    } elseif ($options['roof-type'] != House::ROOF_TYPE_INCLINED) {
                        throw new \InvalidArgumentException(
                            "Invalid roof-type config for a house with a mixed roof"
                        );
                    }
                }
                break;
            case 'attic':
                $amount = $house->getDefaultRoofIfFlat()->getSurface();
                if ($house->getRoofType() == House::ROOF_TYPE_MIXED) {
                    if ($options['roof-type'] == House::ROOF_TYPE_INCLINED) {
                        $amount = $amount * 0.7;
                    } elseif ($options['roof-type'] != House::ROOF_TYPE_INCLINED) {
                        throw new \InvalidArgumentException(
                            "Invalid roof-type config for a house with a mixed roof"
                        );
                    }
                }
                break;
            case 'facade':
            case 'facade_cavity':
            case 'facade_inner':
                $amount = $house->getSurfaceFacade();
                break;
            case 'floor':
            case 'basement':
                $amount = $house->getSurfaceFloor();
                break;
            case 'window_1_1':
            case 'window_0_8':
                $amount = $house->getSurfaceWindow();
                break;
            case 'solar_panels':
                $amount = $this->getSolarPanelCalculatorService()->getSolarPanelsSurfaceForBuildCost($house);
                break;
        }

        return $this->getValue() * $amount;
    }

    /**
     * @return SolarPanelCalculatorService
     */
    public function getSolarPanelCalculatorService()
    {
        return $this->solarPanelCalculatorService;
    }

    public function setSolarPanelCalculatorService(SolarPanelCalculatorService $solarPanelCalculatorService)
    {
        $this->solarPanelCalculatorService = $solarPanelCalculatorService;
    }
}
