<?php

namespace One\CheckJeHuis\Controller;

use One\CheckJeHuis\Entity\ConfigCategory;
use One\CheckJeHuis\Entity\Parameter;
use One\CheckJeHuis\Service\HouseService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use One\CheckJeHuis\Entity\House;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractController extends Controller
{
    const COOKIE_USER_ADDRESS = 'user-address';

    protected function getSessionHouse($create = false, $cityId = false)
    {
        $houseService = $this->getHouseService();
        $house = $houseService->loadHouse();

        $save = false;
        $cookieAddress = $this->get('request')->cookies->get('user-address');

        if (!$house && $create && $cityId) {
            $paramService = $this->get('one.check_je_huis.service.parameter');
            $city = $this->container->get('one.check_je_huis.repository.city_repository')->find($cityId);
            $house = new House($city);
            $defaults = $city->getDefaults();
            $house->setBuildingType($defaults['building_type']);
            $house->setYear($defaults['build_year']);
            $house->setConfigs($houseService->getDefaultConfigs($house));
            $house->setExtraConfigRoof($house->getConfig(ConfigCategory::CAT_ROOF));
            $house->setDefaultSurfaces(
                $houseService->getDefaultSurface($house),
                $houseService->getDefaultRoof($house)
            );
            $house->setDefaultRoofIfFlat($houseService->getDefaultRoofIfFlat($house));
            $house->setDefaultEnergy($houseService->getDefaultEnergy($house));
            $house->setSolarPanelsSurface(
                $paramService->getParameterBySlug(Parameter::PARAM_SOLAR_SURFACE)->getValue()
            );

            $save = true;
        }

        if ($cookieAddress && $house && $cookieAddress != $house->getAddress()) {
            $house->setAddress($cookieAddress);
            $save = true;
        }

        if ($save) {
            $houseService->saveHouse($house);
        }

        return $house;
    }

    /**
     * @return RedirectResponse
     */
    protected function noHouseRedirect()
    {
        return $this->redirect($this->generateUrl('app_index'));
    }

    /**
     * @return HouseService
     */
    protected function getHouseService()
    {
        return $houseService = $this->get('one.check_je_huis.service.house');
    }

    /**
     * @param string $path
     * @param string $packageName
     * @return string
     */
    protected function getAsset($path)
    {
        $path = '/' . $path;
        return $this->container->get('templating.helper.assets')->getUrl($path);
    }

    protected function getContentBySlug(string $slug, $city = null)
    {
        return $this->container->get('one.check_je_huis.repository.content_repository')->getContentBySlug($slug, $city);
    }
}
