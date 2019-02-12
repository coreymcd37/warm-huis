<?php

namespace One\CheckJeHuis\Calculator;

use One\CheckJeHuis\Entity\BuildCost;
use One\CheckJeHuis\Entity\House;
use One\CheckJeHuis\Entity\Subsidy;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class CalculatorFactory implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @return Parameters
     */
    public function createParameters()
    {
        return $this->container->get('one.check_je_huis.service.parameter')->getCalculationParameters();
    }

    /**
     * @param House $house
     * @return CurrentEnergyCalculator
     */
    public function createEnergyCalculatorCurrent(House $house)
    {
        $calculator = new CurrentEnergyCalculator(
            $house,
            self::createParameters(),
            $this->container->get('one.check_je_huis.service.solar_panel_calculator_service')
        );

        $calculator->setDefaultConfigs(
            $this->container->get('one.check_je_huis.service.house')->getDefaultConfigs($house)
        );

        return $calculator;
    }

    /**
     * @param House $house
     * @param State $startEnergy
     * @return UpgradeEnergyCalculator
     */
    public function createEnergyCalculatorUpgrade(House $house)
    {
        $calculator = new UpgradeEnergyCalculator(
            $house,
            self::createParameters(),
            $this->container->get('one.check_je_huis.service.solar_panel_calculator_service')
        );

        return $calculator;
    }

    /**
     * @param House $house
     * @return BuildCostCalculator
     */
    public function createBuildCostCalculator(House $house)
    {
        $calculator = new BuildCostCalculator($house);

        $calculator->setWindRoofCost(
            $this->container->get('one.check_je_huis.service.buildcost')->getCostBySlug(BuildCost::COST_WINDROOF)
        );

        $calculator->setSolarPanelCalculatorService($this->container->get('one.check_je_huis.service.solar_panel_calculator_service'));

        return $calculator;
    }

    /**
     * @param House $house
     * @return SubsidyCalculator
     */
    public function createSubsidyCalculator(House $house)
    {
        $calculator = new SubsidyCalculator($house);
        $calculator->setWindRoofBuildCost(
            $this->container->get('one.check_je_huis.service.buildcost')->getCostBySlug(BuildCost::COST_WINDROOF)
        );
        $calculator->setSolarHeaterBuildCost(
            $this->container->get('one.check_je_huis.service.buildcost')->getCostBySlug(BuildCost::COST_SOLAR_WATER_HEATER)
        );
        $calculator->setWindRoofSubsidies(
            $this->container->get('one.check_je_huis.service.subsidy')->getSubsidiesBySlug(Subsidy::SUBSIDY_WINDROOF, $house->getCity())
        );
        $calculator->setSolarHeaterSubsidies(
            $this->container->get('one.check_je_huis.service.subsidy')->getSubsidiesBySlug(Subsidy::SUBSIDY_SOLAR_HEATER, $house->getCity())
        );

        return $calculator;
    }

    /**
     * @param House $house
     * @param bool $skipUpgrade
     * @return CalculatorView
     */
    public function createCalculatorView(House $house, $skipUpgrade = false)
    {
        $state = State::createFormHouse($house);
        $current = $this->createEnergyCalculatorCurrent($house);
        $current->calculate($state);

        $upgrade = $this->createEnergyCalculatorUpgrade($house);
        $buildCosts = $this->createBuildCostCalculator($house);
        $subsidies = $this->createSubsidyCalculator($house);

        if (!$skipUpgrade) {
            $upgrade->calculate(clone $state);
            $buildCosts->calculate();
            $subsidies->calculate();
        }

        return new CalculatorView($house, $current, $upgrade, $buildCosts, $subsidies, $this->createParameters());
    }
}
