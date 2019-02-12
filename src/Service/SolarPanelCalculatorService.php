<?php

namespace One\CheckJeHuis\Service;

use One\CheckJeHuis\Entity\House;
use One\CheckJeHuis\Entity\Parameter;

class SolarPanelCalculatorService
{
    /**
     * @var ParameterService
     */
    private $parameterService;

    public function __construct(ParameterService $parameterService)
    {
        $this->parameterService = $parameterService;
    }

    public function calculate($name, $value)
    {
        $solarPeakEntity = $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_PEAK_SINGLE);
        $solarSingleSurfaceEntity = $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_PANEL_SURFACE_SINGLE);
        $solarPeak = $solarPeakEntity->getValue();
        $solarSingleSurface = $solarSingleSurfaceEntity->getValue();

        $solarPanelValues = [];
        switch ($name) {
            case 'solar_surface' :
                $solarPanelCount = (int)($value / $solarSingleSurface);
                $solarPanelValues = [
                    'surface' => (float) $value,
                    'count' => $solarPanelCount,
                    'peak' => $solarPanelCount * $solarPeak,
                ];
                break;
            case 'solar_count' :
                $solarPanelValues = [
                    'surface' => (float)$value * $solarSingleSurface,
                    'count' => (float)$value,
                    'peak' => (float)$value * $solarPeak,
                ];
                break;
            case 'solar_peak' :
                $solarPanelCount = (int)($value / $solarPeak);
                $solarPanelValues = [
                    'surface' => $solarPanelCount * $solarSingleSurface,
                    'count' => $solarPanelCount,
                    'peak' => (float)$value,
                ];
                break;
        }

        return $solarPanelValues;
    }

    public function getSolarPanelInfo(House $house)
    {
        if ($house->getSolarPanelCount() != 0) {
            return [
                'surface' => $house->getSolarPanelCount() * $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_PANEL_SURFACE_SINGLE)->getValue(),
                'count' => $house->getSolarPanelCount(),
                'peak' => $house->getSolarPanelCount() * $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_PEAK_SINGLE)->getValue(),
            ];
        }

        if ($house->getSolarPanelPeak() != 0) {
            $solarPanelCount = (int)($house->getSolarPanelPeak() / $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_PEAK_SINGLE)->getValue());

            return [
                'surface' => $solarPanelCount * $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_PANEL_SURFACE_SINGLE)->getValue(),
                'count' => $solarPanelCount,
                'peak' => (float)$house->getSolarPanelPeak(),
            ];
        }

        $solarPanelCount = (int)($house->getSolarPanelsSurface() / $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_PANEL_SURFACE_SINGLE)->getValue());

        return [
            'surface' => $house->getSolarPanelsSurface(),
            'count' => $solarPanelCount,
            'peak' => $solarPanelCount * $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_PEAK_SINGLE)->getValue(),
        ];
    }


    public function getSolarPanelEnergyAmount(House $house, $amount)
    {
        if ($house->getSolarPanelCount() != 0) {
            return $house->getSolarPanelCount() * $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_PEAK_SINGLE)->getValue() * ($this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_YIELD)->getValue() / 100);
        }

        if ($house->getSolarPanelPeak() != 0) {
            return $house->getSolarPanelPeak() * $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_YIELD)->getValue() / 100;
        }

        return $house->getSolarPanelsSurface() * $amount;
    }

    public function getSolarPanelsSurfaceForBuildCost(House $house)
    {
        if ($house->getSolarPanelCount() != 0) {
            return $house->getSolarPanelCount() * $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_PANEL_SURFACE_SINGLE)->getValue();
        }

        if ($house->getSolarPanelPeak() != 0) {
            $solarPanelCount = (int)($house->getSolarPanelPeak() / $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_PEAK_SINGLE)->getValue());
            return $solarPanelCount * $this->parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_PANEL_SURFACE_SINGLE)->getValue();
        }

        return $house->getSolarPanelsSurface();
    }

}