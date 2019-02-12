<?php

namespace One\CheckJeHuis\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class House
{
    const BUILDING_TYPE_OPEN    = 'open';
    const BUILDING_TYPE_CORNER  = 'corner';
    const BUILDING_TYPE_CLOSED  = 'closed';

    const BUILDING_SIZE_LARGE   = 'large';
    const BUILDING_SIZE_MEDIUM  = 'medium';
    const BUILDING_SIZE_SMALL   = 'small';

    const ROOF_TYPE_INCLINED    = 'yes';
    const ROOF_TYPE_FLAT        = 'no';
    const ROOF_TYPE_MIXED       = 'mixed';

    const OWNERSHIP_OWNER       = 'owner';
    const OWNERSHIP_RENTER      = 'renter';
    const OWNERSHIP_LETTER      = 'letter';

    const EMAIL_ANONYMOUS = 'anoniem@warmhuis.be';

    const ADDRESS_ANONYMOUS = 'anoniem adres';

    protected $id;

    protected $token;

    protected $lastKnownRoute;

    protected $lastUpdate;

    protected $address;

    protected $newsletter;

    protected $email;

    protected $buildingType;

    protected $roofType;

    protected $size;

    protected $ownership;

    protected $year;

    protected $occupants;

    protected $defaultEnergy;

    protected $consumptionGas;

    protected $consumptionElec;

    protected $consumptionOil;

    protected $defaultSurface;

    protected $defaultRoof;

    protected $defaultRoofIfFlat;

    protected $surfaceLivingArea;

    protected $surfaceFloor;

    protected $surfaceFacade;

    protected $surfaceWindow;

    protected $surfaceRoof;

    protected $surfaceRoofExtra;

    protected $electricHeating;

    protected $hasWindroof;

    protected $placeWindroof;

    protected $configs;

    protected $city;

    protected $postalCode;

    protected $cityName;

    /**
     * In case of a mixed roof, this holds the config of the flat part
     */
    protected $extraConfigRoof;

    /**
     * In case of a mixed roof, this holds the upgrade config of the flat part
     */
    protected $extraUpgradeRoof;

    protected $renewables;

    protected $upgradeConfigs;

    protected $upgradeRenewables;

    /**
     * THIS IS NOW THE SURFACE
     *
     * @todo rename!
     */
    protected $solarPanelsKWHPiek;
    protected $solarPanelPeak = 0;
    protected $solarPanelCount = 0;

    protected $visitedRoutes = [];

    public function __construct(City $city)
    {
        $this->token = uniqid();

        $this->configs              = new ArrayCollection();
        $this->renewables           = new ArrayCollection();
        $this->upgradeConfigs       = new ArrayCollection();
        $this->upgradeRenewables    = new ArrayCollection();

        $this->size                 = self::BUILDING_SIZE_MEDIUM;
        $this->roofType             = self::ROOF_TYPE_INCLINED;
        $this->ownership            = 'owner';
        $this->occupants            = 4;
        $this->electricHeating      = false;
        $this->hasWindroof          = true;
        $this->placeWindroof        = false;
        $this->solarPanelsKWHPiek   = 30;
        $this->city = $city;
    }

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
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return bool
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @param bool$newsletter
     * @return $this
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;
        return $this;
    }

    /**
     * @return string
     */
    public function getBuildingType()
    {
        return $this->buildingType;
    }

    /**
     * @param string $buildingType
     * @return $this
     */
    public function setBuildingType($buildingType)
    {
        $this->buildingType = $buildingType;
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasElectricHeating()
    {
        return $this->electricHeating;
    }

    /**
     * @param boolean $electricHeating
     * @return $this
     */
    public function setElectricHeating($electricHeating)
    {
        $this->electricHeating = $electricHeating;

        // remove invalid heating configs if present
        $slug = ($electricHeating) ? ConfigCategory::CAT_HEATING: ConfigCategory::CAT_HEATING_ELEC;
        foreach ($this->configs as $c) {
            if ($c->getCategory()->getSlug() == $slug) {
                $this->configs->removeElement($c);
            }
        }
        foreach ($this->upgradeConfigs as $c) {
            if ($c->getCategory()->getSlug() == $slug) {
                $this->upgradeConfigs->removeElement($c);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail($force = false)
    {
        if ($force === false && $this->email === self::EMAIL_ANONYMOUS) {
            return '';
        }

        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return int
     */
    public function getOccupants()
    {
        return $this->occupants;
    }

    /**
     * @param int $occupants
     * @return $this
     */
    public function setOccupants($occupants)
    {
        $this->occupants = $occupants;
        return $this;
    }

    /**
     * @return string
     */
    public function getOwnership()
    {
        return $this->ownership;
    }

    /**
     * @param string $ownership
     * @return $this
     */
    public function setOwnership($ownership)
    {
        $this->ownership = $ownership;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoofType()
    {
        return $this->roofType;
    }

    /**
     * @param string $roofType
     * @return $this
     */
    public function setRoofType($roofType)
    {
        $this->roofType = $roofType;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param string $year
     * @return $this
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @param bool $default
     * @return float|null
     */
    public function getConsumptionElec($default = true)
    {
        if (!$this->consumptionElec && $default) {
            return $this->getDefaultEnergy()->getElectricity();
        }
        return $this->consumptionElec;
    }

    /**
     * @param float $consumptionElec
     * @return $this
     */
    public function setConsumptionElec($consumptionElec)
    {
        $this->consumptionElec = $consumptionElec;
        return $this;
    }

    /**
     * @param bool $default
     * @return float|null
     */
    public function getConsumptionGas($default = true)
    {
        if (!$this->consumptionGas && $default) {
            return $this->getDefaultEnergy()->getGas();
        }

        return $this->consumptionGas;
    }

    public function getConsumptionOil($default = true)
    {
        if (!$this->consumptionOil && $default) {
            return $this->getDefaultEnergy()->getOil();
        }

        return $this->consumptionOil;
    }

    /**
     * @param mixed $consumptionOil
     */
    public function setConsumptionOil($consumptionOil)
    {
        $this->consumptionOil = $consumptionOil;
    }

    /**
     * @param float $consumptionGas
     * @return $this
     */
    public function setConsumptionGas($consumptionGas)
    {
        $this->consumptionGas = $consumptionGas;
        return $this;
    }

    /**
     * @param Renewable[] $renewables
     * @return $this
     */
    public function setRenewables($renewables)
    {
        $this->renewables = $renewables;
        return $this;
    }

    /**
     * @param bool $default
     * @return float|null
     */
    public function getSurfaceFacade($default = true)
    {
        if (!$this->surfaceFacade && $default) {
            return $this->getDefaultSurface()->getFacade();
        }
        return $this->surfaceFacade;
    }

    /**
     * @param float $surfaceFacade
     * @return $this
     */
    public function setSurfaceFacade($surfaceFacade)
    {
        $this->surfaceFacade = $surfaceFacade;
        return $this;
    }

    /**
     * @param bool $default
     * @return float|mixed
     */
    public function getSurfaceLivingArea($default = true)
    {
        if ($default && !$this->surfaceLivingArea) {
            return $this->getDefaultSurface()->getLivingArea();
        }

        return $this->surfaceLivingArea;
    }

    /**
     * @param float $surfaceLivingArea
     * @return $this
     */
    public function setSurfaceLivingArea($surfaceLivingArea)
    {
        $this->surfaceLivingArea = $surfaceLivingArea;

        if ($surfaceLivingArea < 175) {
            $this->setSize(self::BUILDING_SIZE_SMALL);
        } elseif ($surfaceLivingArea < 250) {
            $this->setSize(self::BUILDING_SIZE_MEDIUM);
        } else {
            $this->setSize(self::BUILDING_SIZE_LARGE);
        }

        return $this;
    }

    /**
     * @param bool $default
     * @return float
     */
    public function getSurfaceFloor($default = true)
    {
        if (!$this->surfaceFloor && $default) {
            return $this->getDefaultSurface()->getFloor();
        }
        return $this->surfaceFloor;
    }

    /**
     * @param float $surfaceFloor
     * @return $this
     */
    public function setSurfaceFloor($surfaceFloor)
    {
        $this->surfaceFloor = $surfaceFloor;

        return $this;
    }

    /**
     * @param bool $default
     * @return float
     */
    public function getSurfaceRoof($default = true, Config $config = null)
    {
        if (!$this->surfaceRoof && $default) {
            // if we are placing attic floor insulation
            // we need to have the flat surface area
            // add check to see if this was set, for BC reasons
            $surface = $this->getDefaultRoof()->getSurface();
            if ($config && $config->getId() === Config::CONFIG_ATTIC_FLOOR && $this->getDefaultRoofIfFlat()) {
                $surface = $this->getDefaultRoofIfFlat()->getSurface();
            }
            if ($this->getRoofType() === House::ROOF_TYPE_MIXED) {
                return round($surface * 0.7);
            }
            return $surface;
        }
        return $this->surfaceRoof;
    }

    /**
     * @param float $surfaceRoof
     * @return $this
     */
    public function setSurfaceRoof($surfaceRoof)
    {
        $this->surfaceRoof = $surfaceRoof;
        return $this;
    }

    /**
     * @param bool $default
     * @return float|null
     */
    public function getSurfaceWindow($default = true)
    {
        if (!$this->surfaceWindow && $default) {
            return $this->getDefaultSurface()->getWindow();
        }
        return $this->surfaceWindow;
    }

    /**
     * @param float $surfaceWindow
     * @return $this
     */
    public function setSurfaceWindow($surfaceWindow)
    {
        $this->surfaceWindow = $surfaceWindow;
        return $this;
    }

    /**
     * With a mixed roof, this is the set value or 30% of the default roof surface
     *
     * @param bool $default
     * @return float|int
     */
    public function getSurfaceRoofExtra($default = true)
    {
        if ($this->surfaceRoofExtra) {
            return $this->surfaceRoofExtra;
        }

        if ($default) {
            if ($this->getRoofType() == House::ROOF_TYPE_MIXED) {
                $surface = $this->getDefaultRoof()->getSurface();
                return round($surface * 0.3);
            }
        }

        return 0;
    }

    /**
     * @param float $surface
     * @return $this
     */
    public function setSurfaceRoofExtra($surface = null)
    {
        $this->surfaceRoofExtra = $surface;
        return $this;
    }

    public function setDefaultSurfaces(DefaultSurface $defaultSurface, DefaultRoof $defaultRoof)
    {
        $this->surfaceFloor     = null;
        $this->surfaceFacade    = null;
        $this->surfaceWindow    = null;
        $this->surfaceRoof      = null;
        $this->defaultSurface   = $defaultSurface;
        $this->defaultRoof      = $defaultRoof;
        return $this;
    }

    /**
     * @return DefaultSurface
     */
    public function getDefaultSurface()
    {
        return $this->defaultSurface;
    }

    /**
     * @param DefaultSurface $defaultSurface
     * @return $this
     */
    public function setDefaultSurface(DefaultSurface $defaultSurface)
    {
        $this->defaultSurface = $defaultSurface;
        return $this;
    }

    /**
     * Returns the divergence between the default and actual surface for a category
     * returns a float with value:
     *   - 1 if the surfaces are equal
     *   - between 0 and 1 if the surface is smaller
     *   - larger than 1 if the surface is bigger
     *
     * @param string $category
     * @param int $percent between 1 and 100
     * @return float
     */
    public function getSurfaceDiffPercentage($category, $percent = 100)
    {
        $default = 0;
        $actual = 0;

        switch ($category) {
            case ConfigCategory::CAT_ROOF:
                if ($percent < 50) {
                    $actual     = $this->getSurfaceRoofExtra();
                } else {
                    $actual     = $this->getSurfaceRoof();
                }
                $default    = $this->getDefaultRoof()->getSurface();
                break;
            case ConfigCategory::CAT_FACADE:
                $actual     = $this->getSurfaceFacade();
                $default    = $this->getDefaultSurface()->getFacade();
                break;
            case ConfigCategory::CAT_FLOOR:
                $actual     = $this->getSurfaceFloor();
                $default    = $this->getDefaultSurface()->getFloor();
                break;
            case ConfigCategory::CAT_WINDOWS:
                $actual     = $this->getSurfaceWindow();
                $default    = $this->getDefaultSurface()->getWindow();
                break;
        }

        $diff = 1;
        $percent = ($percent / 100);
        $default = ($default * $percent);
        if ($default && $actual && ($actual < $default - 1 || $actual > $default +1)) {
            $diff = $actual / $default;
        }

        return $diff;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return bool
     */
    public function canHaveWindRoof()
    {
        return (
            $this->getRoofType() === self::ROOF_TYPE_MIXED || $this->getRoofType() === self::ROOF_TYPE_INCLINED
        ) && (
            ($this->getUpgradeConfig(ConfigCategory::CAT_ROOF) && $this->getUpgradeConfig(ConfigCategory::CAT_ROOF)->getId() !== Config::CONFIG_ATTIC_FLOOR) ||
            (!$this->getUpgradeConfig(ConfigCategory::CAT_ROOF) && $this->getConfig(ConfigCategory::CAT_ROOF)->getId() !== Config::CONFIG_ATTIC_FLOOR)
        );
    }

    /**
     * @return boolean
     */
    public function hasWindRoof()
    {
        return $this->hasWindroof;
    }

    /**
     * @param boolean $hasWindroof
     * @return $this
     */
    public function setHasWindRoof($hasWindroof)
    {
        $this->hasWindroof = $hasWindroof;

        // if we have one, we can't place one anymore
        if ($this->hasWindroof) {
            $this->setPlaceWindroof(false);
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPlaceWindroof()
    {
        return $this->placeWindroof;
    }

    /**
     * @param boolean $placeWindroof
     * @return $this
     */
    public function setPlaceWindroof($placeWindroof)
    {
        $this->placeWindroof = $placeWindroof;
        return $this;
    }

    public function placeWindRoofWithoutInsulationChange()
    {
        if (!$this->placeWindroof) {
            return false;
        }

        foreach ($this->upgradeConfigs as $c) {
            if ($c->getCategory()->getSlug() == ConfigCategory::CAT_ROOF) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return float
     */
    public function getSolarPanelsKWHPiek()
    {
        return $this->solarPanelsKWHPiek;
    }

    /**
     * @param float $solarPanelsKWHPiek
     * @return $this
     */
    public function setSolarPanelsKWHPiek($solarPanelsKWHPiek)
    {
        $this->solarPanelsKWHPiek = $solarPanelsKWHPiek * 15;
        return $this;
    }

    /**
     * @param float|int $surface
     * @return $this
     */
    public function setSolarPanelsSurface($surface)
    {
        $this->solarPanelsKWHPiek = $surface;
        return $this;
    }

    /**
     * @return float|int
     */
    public function getSolarPanelsSurface()
    {
        return $this->solarPanelsKWHPiek;
    }


    /**
     * @return Config[]|ArrayCollection
     */
    public function getAllConfigs()
    {
        return $this->configs;
    }

    /**
     * @return Config[]
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * @param Config[] $configs
     * @return $this
     */
    public function setConfigs($configs)
    {
        foreach ($configs as $c) {
            $this->addConfig($c);
        }

        $this->resetUpgrades();

        return $this;
    }

    /**
     * Only one Config per ConfigCategory allowed
     *
     * @param Config $config
     * @return $this
     */
    public function addConfig(Config $config)
    {
        foreach ($this->configs as $c) {
            if ($c->getCategory() == $config->getCategory()) {
                $this->configs->removeElement($c);
            }
        }
        $this->configs->add($config);

        $this->resetUpgrades();

        return $this;
    }

    /**
     * @param ConfigCategory|string $category ConfigCategory instance or a slug
     * @return Config
     * @throws \RuntimeException
     */
    public function getConfig($category)
    {
        if ($category instanceof ConfigCategory) {
            $category = $category->getSlug();
        }

        foreach ($this->configs as $c) {
            if ($c->getCategory()->getSlug() == $category) {
                return $c;
            }
        }

        throw new \RuntimeException('House has no configuration for category: ' . $category);
    }

    /**
     * @param ConfigCategory $category
     * @return Config
     * @throws \RuntimeException
     */
    public function hasConfig($category)
    {
        if ($category instanceof ConfigCategory) {
            $category = $category->getSlug();
        }

        foreach ($this->configs as $c) {
            if ($c->getCategory()->getSlug() == $category) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $default
     * @return Config|null
     */
    public function getExtraConfigRoof($default = false)
    {
        if (($default && !$this->extraConfigRoof) || $this->getRoofType() !== self::ROOF_TYPE_MIXED) {
            foreach ($this->configs as $c) {
                if ($c->getCategory()->getSlug() === ConfigCategory::CAT_ROOF) {
                    return $c;
                }
            }
        }
        return $this->extraConfigRoof;
    }

    /**
     * @param mixed $extraConfigRoof
     * @return $this
     */
    public function setExtraConfigRoof($extraConfigRoof)
    {
        $this->extraConfigRoof = $extraConfigRoof;
        return $this;
    }

    /**
     * @param $default
     * @return Config
     */
    public function getExtraUpgradeRoof($default = false)
    {
        if ($default && ($this->getRoofType() !== self::ROOF_TYPE_MIXED || !$this->extraUpgradeRoof)) {
            foreach ($this->configs as $c) {
                if ($c->getCategory()->getSlug() === ConfigCategory::CAT_ROOF) {
                    return $c;
                }
            }
        }
        return $this->extraUpgradeRoof;
    }

    /**
     * @param Config $extraUpgradeRoof
     * @return $this
     */
    public function setExtraUpgradeRoof($extraUpgradeRoof)
    {
        $this->extraUpgradeRoof = $extraUpgradeRoof;
        return $this;
    }

    /**
     * @return Renewable[]
     */
    public function getRenewables()
    {
        return $this->renewables;
    }

    /**
     * @param Renewable $renewable
     */
    public function addRenewable(Renewable $renewable)
    {
        if (!$this->hasRenewable($renewable)) {
            $this->renewables->add($renewable);
            $this->upgradeRenewables->removeElement($renewable);
        }

        $this->resetUpgrades();
    }

    /**
     * @param Renewable $renewable
     * @return bool
     */
    public function hasRenewable(Renewable $renewable)
    {
        foreach ($this->renewables as $r) {
            if ($r == $renewable) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Renewable $renewable
     */
    public function removeRenewable(Renewable $renewable)
    {
        if ($this->hasRenewable($renewable)) {
            $this->renewables->removeElement($renewable);
        }

        $this->resetUpgrades();
    }

    /**
     * @param Config $config
     */
    public function removeConfig(Config $config)
    {
        if (!$this->configs->contains($config)) {
            return;
        }
        $this->configs->removeElement($config);
    }

    /**
     * @return Config[]|ArrayCollection
     */
    public function getAllUpgradeConfigs()
    {
        return $this->upgradeConfigs;
    }

    /**
     * @return Config[]
     */
    public function getUpgradeConfigs()
    {
        return $this->upgradeConfigs;
    }

    /**
     * @param Config $configs
     * @return $this
     */
    public function setUpgradeConfigs($configs)
    {
        $this->upgradeConfigs = $configs;
        $this->validateUpgradeConfigs();

        return $this;
    }

    /**
     * Only one Config per ConfigCategory allowed
     *
     * @param Config $config
     * @return $this
     */
    public function addUpgradeConfig(Config $config)
    {
        foreach ($this->upgradeConfigs as $c) {
            if ($c->getCategory() == $config->getCategory()) {
                $this->upgradeConfigs->removeElement($c);
            }
        }
        $this->upgradeConfigs->add($config);
        $this->validateUpgradeConfigs();

        return $this;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function removeUpgradeConfig(Config $config)
    {
        $this->upgradeConfigs->removeElement($config);

        return $this;
    }

    /**
     * @param ConfigCategory|string $category
     * @return Config|null
     */
    public function getUpgradeConfig($category)
    {
        if ($category instanceof ConfigCategory) {
            $category = $category->getSlug();
        }

        foreach ($this->upgradeConfigs as $c) {
            if ($c->getCategory()->getSlug() === $category) {
                return $c;
            }
        }
    }

    /**
     * @return Renewable[]
     */
    public function getUpgradeRenewables()
    {
        return $this->upgradeRenewables;
    }

    public function addUpgradeRenewable(Renewable $renewable)
    {
        if ($this->hasRenewable($renewable)) {
            return;
        }

        if (!$this->hasUpgradeRenewable($renewable)) {
            $this->upgradeRenewables[] = $renewable;
        }
    }

    public function hasUpgradeRenewable(Renewable $renewable)
    {
        foreach ($this->upgradeRenewables as $r) {
            if ($r == $renewable) {
                return true;
            }
        }
        return false;
    }

    public function removeUpgradeRenewable(Renewable $renewable)
    {
        if ($this->hasUpgradeRenewable($renewable)) {
            $this->upgradeRenewables->removeElement($renewable);
        }
    }

    /**
     * @return Renewable[]
     */
    public function getAllRenewables()
    {
        $renewables = array();

        foreach ($this->getRenewables() as $r) {
            $renewables[$r->getSlug()] = $r;
        }
        foreach ($this->getUpgradeRenewables() as $r) {
            $renewables[$r->getSlug()] = $r;
        }

        return $renewables;
    }

    /**
     * @return DefaultEnergy
     */
    public function getDefaultEnergy()
    {
        return $this->defaultEnergy;
    }

    /**
     * @param DefaultEnergy $defaultEnergy
     * @return $this
     */
    public function setDefaultEnergy(DefaultEnergy $defaultEnergy)
    {
        $this->defaultEnergy    = $defaultEnergy;
        return $this;
    }

    /**
     * @return DefaultRoof
     */
    public function getDefaultRoof()
    {
        return $this->defaultRoof;
    }

    /**
     * @param DefaultRoof $defaultRoof
     * @return $this
     */
    public function setDefaultRoof(DefaultRoof $defaultRoof)
    {
        $this->defaultRoof = $defaultRoof;
        return $this;
    }

    /**
     * @return DefaultRoof
     */
    public function getDefaultRoofIfFlat()
    {
        return $this->defaultRoofIfFlat;
    }

    /**
     * @param DefaultRoof $defaultRoofIfFlat
     * @return $this
     */
    public function setDefaultRoofIfFlat($defaultRoofIfFlat)
    {
        $this->defaultRoofIfFlat = $defaultRoofIfFlat;
        return $this;
    }

    public function hasCustomEnergy()
    {
        return $this->getConsumptionElec(false) || $this->getConsumptionGas(false) || $this->getConsumptionOil(false);
    }

    public function hasCustomSurfaces()
    {
        return
            $this->getSurfaceFloor(false) ||
            $this->getSurfaceFacade(false) ||
            $this->getSurfaceWindow(false) ||
            $this->getSurfaceRoof(false)
        ;
    }

    /**
     * @return string
     */
    public function getLastKnownRoute()
    {
        return $this->lastKnownRoute;
    }

    /**
     * @param string $lastKnownRoute
     * @return $this
     */
    public function setLastKnownRoute($lastKnownRoute)
    {
        $this->lastKnownRoute = $lastKnownRoute;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate()
    {
        $this->lastUpdate = new \DateTime();
    }

    /**
     * reset all upgrade configs and renewables
     */
    public function resetUpgrades()
    {
        $this->upgradeConfigs       = new ArrayCollection();
        $this->upgradeRenewables    = new ArrayCollection();
        $this->extraUpgradeRoof     = null;
        $this->surfaceFacade        = null;
        $this->surfaceRoof          = null;
        $this->surfaceRoofExtra     = null;
        $this->surfaceWindow        = null;
    }

    /**
     * assert if all upgrade configs are still valid after a change
     * in defaults or current configs
     */
    public function validateUpgradeConfigs()
    {
        foreach ($this->getUpgradeConfigs() as $upgrade) {
            // if we have an upgrade lower or equal than a current config, remove it
            if ($this->hasConfig($upgrade->getCategory()) &&
                $upgrade->getOrdering() <= $this->getConfig($upgrade->getCategory())->getOrdering()
            ) {
                $this->removeUpgradeConfig($upgrade);
            }
        }

        // if we are placing attic floor insulation, disable the wind roof
        $roof = $this->getUpgradeConfig(ConfigCategory::CAT_ROOF);
        if ($roof && $roof->getId() === Config::CONFIG_ATTIC_FLOOR) {
            $this->setPlaceWindroof(false);
        }
    }

    public function isHeatPumpAllowed()
    {
        // if heat pump is already present
        $heating = $this->hasElectricHeating() ? ConfigCategory::CAT_HEATING_ELEC: ConfigCategory::CAT_HEATING;
        if ($this->getConfig($heating) && in_array($this->getConfig($heating)->getId(), [
                37, 38, 40, 41
            ])
        ) {
            return true;
        }

        $configRoof = $this->getUpgradeConfig(ConfigCategory::CAT_ROOF) ?: $this->getConfig(ConfigCategory::CAT_ROOF);
        $configWindow = $this->getUpgradeConfig(ConfigCategory::CAT_WINDOWS) ?: $this->getConfig(ConfigCategory::CAT_WINDOWS);

        return $configRoof->isPossibleUpgrade() && (
            $this->getRoofType() !== House::ROOF_TYPE_MIXED || $this->getExtraUpgradeRoof(true)->isPossibleUpgrade()
        ) && $configWindow->isPossibleUpgrade();
    }

    /**
     * Returns the possible building types
     *
     * @return array
     */
    public static function getBuildingTypes()
    {
        return array(
            self::BUILDING_TYPE_OPEN    => 'Open bebouwing',
            self::BUILDING_TYPE_CORNER  => 'Halfopen bebouwing',
            self::BUILDING_TYPE_CLOSED  => 'Gesloten bebouwing',
        );
    }

    /**
     * Returns the possible roof types
     *
     * @return array
     */
    public static function getRoofTypes()
    {
        return array(
            self::ROOF_TYPE_MIXED       => 'gemengd',
            self::ROOF_TYPE_INCLINED    => 'schuin',
            self::ROOF_TYPE_FLAT        => 'plat',
        );
    }

    /**
     * Returns the possible year options
     *
     * @return array
     */
    public static function getYears()
    {
        return array(
            '1900'  => '< 1900',
            '1945'  => '1901 - 1945',
            '1970'  => '1946 - 1970',
            '2000'  => '1971 - 2000',
            '3000'     => '> 2000',
        );
    }

    /**
     * Returns the possible building sizes
     *
     * @return array
     */
    public static function getSizes()
    {
        return array(
            self::BUILDING_SIZE_LARGE   => 'Groot',
            self::BUILDING_SIZE_MEDIUM  => 'Middel',
            self::BUILDING_SIZE_SMALL   => 'Klein',
        );
    }

    /**
     * Returns the possible ownership options
     *
     * @return array
     */
    public static function getOwnerships()
    {
        return array(
            self::OWNERSHIP_OWNER   => 'eigenaar en bewoner',
            self::OWNERSHIP_RENTER  => 'huurder',
            // self::OWNERSHIP_LETTER  => 'verhuurder',
        );
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return array
     */
    public function getVisitedRoutes()
    {
        return $this->visitedRoutes;
    }

    /**
     * @param array $visitedRoutes
     */
    public function setVisitedRoutes(array $visitedRoutes)
    {
        $this->visitedRoutes = $visitedRoutes;
    }

    public function getSolarPanelPeak()
    {
        return $this->solarPanelPeak;
    }

    public function getSolarPanelCount()
    {
        return $this->solarPanelCount;
    }
    /**
     * @param float|int $count
     * @return $this
     */
    public function setSolarPanelCount($count)
    {
        $this->solarPanelCount = $count;
        return $this;
    }
    /**
     * @param float|int $peak
     * @return $this
     */
    public function setSolarPanelPeak($peak)
    {
        $this->solarPanelPeak = $peak;
        return $this;
    }

    public function setSolarPanelParams($name, $value)
    {
        $this->setSolarPanelPeak(0);
        $this->setSolarPanelCount(0);
        switch ($name) {
            case 'solar_surface' :
                $this->setSolarPanelsSurface($value);
                $this->setSolarPanelCount(0);
                $this->setSolarPanelPeak(0);
                break;
            case 'solar_count' :
                $this->setSolarPanelsSurface(0);
                $this->setSolarPanelCount($value);
                $this->setSolarPanelPeak(0);
                break;
            case 'solar_peak' :
                $this->setSolarPanelsSurface(0);
                $this->setSolarPanelCount(0);
                $this->setSolarPanelPeak($value);
                break;
        }
    }

    public function getPostalCode()
    {
        return $this->postalCode;
    }

    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    public function getCityName()
    {
        return $this->cityName;
    }

    public function setCityName($cityName)
    {
        $this->cityName = $cityName;
    }
}
