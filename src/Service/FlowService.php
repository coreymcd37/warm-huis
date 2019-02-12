<?php

namespace One\CheckJeHuis\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class FlowService
{
    protected $houseFlow = [
        'house_config_roof',
        'house_config_facade',
        'house_config_floor',
        'house_config_window',
        'house_config_ventilation',
        'house_config_heating',
        'house_config_renewable',
        'house_energy_summary'
    ];

    protected $currentRequest;

    private $houseService;

    public function __construct(RequestStack $requestStack, HouseService $houseService)
    {
        $this->currentRequest = $requestStack->getCurrentRequest();
        $this->houseService = $houseService;
    }

    public function getNextStep($currentRoute)
    {
        $currentStep = array_search($currentRoute, $this->houseFlow);
        $nextStep = $currentStep + 1;
        if (!isset($this->houseFlow[$nextStep])) {
            throw new \Exception('No next route found');
        }

        return $this->houseFlow[$nextStep];
    }

    public function canGoToRoute($route)
    {
        $visitedRoutes = $this->getVisitedRoutes();

        if ($route === 'house_calculator') {
            $nonVisitedRoutes = array_diff($this->houseFlow, $visitedRoutes);
            if (empty($nonVisitedRoutes)) {
                return true;
            }
        }

        return false;
    }

    protected function getVisitedRoutes()
    {
        $house = $this->houseService->loadHouse();
        if (!$house) {
            return [];
        }

        return $house->getVisitedRoutes();
    }

    public function saveStep($route)
    {
        $visitedRoutes = $this->getVisitedRoutes();
        if (!in_array($route, $this->houseFlow, true)) {
            return $visitedRoutes;
        }

        if (!in_array($route, $visitedRoutes, true)) {
            $visitedRoutes[] = $route;
            $house = $this->houseService->loadHouse();
            if (!$house) {
                return $visitedRoutes;
            }
            $house->setVisitedRoutes($visitedRoutes);
            $this->houseService->saveHouse($house);
        }

        return $visitedRoutes;
    }
}