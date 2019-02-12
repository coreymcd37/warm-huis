<?php

namespace One\CheckJeHuis\Controller;

use One\CheckJeHuis\Calculator\CalculatorView;
use One\CheckJeHuis\Entity\Config;
use One\CheckJeHuis\Entity\ConfigCategory;
use One\CheckJeHuis\Entity\Content;
use One\CheckJeHuis\Entity\House;
use One\CheckJeHuis\Entity\Parameter;
use One\CheckJeHuis\Entity\Renewable;
use One\CheckJeHuis\EventListener\Event\HousesExportEvent;
use One\CheckJeHuis\Form\HouseEmailType;
use One\CheckJeHuis\Form\MailPlanType;
use One\CheckJeHuis\Repository\ConfigCategoryRepository;
use One\CheckJeHuis\Repository\ConfigRepository;
use One\CheckJeHuis\Repository\RenewableRepository;
use One\CheckJeHuis\Service\HouseService;
use One\CheckJeHuis\Service\SolarPanelCalculatorService;
use One\CheckJeHuis\Utility\Format;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HouseController extends AbstractController
{
    private $configRepository;
    private $renewableRepository;
    private $configCategoryRepository;
    private $solarPanelCalculatorService;

    public function __construct(
        ConfigRepository $configRepository,
        RenewableRepository $renewableRepository,
        ConfigCategoryRepository $configCategoryRepository,
        SolarPanelCalculatorService $solarPanelCalculatorService
    )
    {
        $this->configRepository = $configRepository;
        $this->renewableRepository = $renewableRepository;
        $this->configCategoryRepository = $configCategoryRepository;
        $this->solarPanelCalculatorService = $solarPanelCalculatorService;
    }

    /**
     * @var CalculatorView
     */
    protected $calculatorView;

    /**
     * MIJN HUIS
     */

    public function buildingTypeAction(Request $request)
    {
        $cityId = $request->getSession()->get('city');
        $house = $this->getSessionHouse(true, $cityId);
        if (!$house) {
            return $this->noHouseRedirect();
        }

        if ($request->isMethod('post')) {
            if ($house->getBuildingType() != $request->request->get('building-type')) {
                $house->setBuildingType($request->request->get('building-type'));
                $this->getHouseService()->saveHouse($house, true);
            }

            return $this->redirect($this->generateUrl('house_year'));
        }

        $buildingTypes = House::getBuildingTypes();
        $options = array();
        foreach ($buildingTypes as $type => $label) {
            $options[] = array(
                'value'     => $type,
                'label'     => $label,
                'icon'      => $this->getAsset('images/icons/house-' . $type . '.svg'),
                'active'    => $house->getBuildingType() == $type
            );
        }

        $this->saveHouseLastRoute($request);

        return $this->render(':House/Basics:building-type.html.twig', array(
            'house'         => $house,
            'options'       => $options,
            'content'       => $this->getContentBySlug(Content::ONE_TYPE),
        ));
    }

    public function yearAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        if ($request->isMethod('post')) {
            if ($house->getYear() != $request->get('year')) {
                $house->setYear($request->get('year'));
                $this->getHouseService()->saveHouse($house, true);
            }

            return $this->redirect($this->generateUrl('house_roof'));
        }

        $options = House::getYears();

        $this->saveHouseLastRoute($request);

        return $this->render(':House/Basics:year.html.twig', array(
            'house'         => $house,
            'options'       => $options,
            'content'       => $this->getContentBySlug(Content::ONE_YEAR),
        ));
    }

    public function roofTypeAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        if ($request->isMethod('post')) {
            if ($house->getRoofType() != $request->get('roof-type')) {
                $house->setRoofType($request->get('roof-type'));
                $this->getHouseService()->saveHouse($house, true);
            }

            return $this->redirect($this->generateUrl('house_surface'));
        }

        $roofTypes = House::getRoofTypes();
        $options = array();
        $icons = array(
            House::ROOF_TYPE_INCLINED   => 'inclined',
            House::ROOF_TYPE_FLAT       => 'flat',
            House::ROOF_TYPE_MIXED      => 'mixed',
        );
        foreach ($roofTypes as $type => $label) {
            $options[] = array(
                'value'     => $type,
                'label'     => $label,
                'icon'      => $this->getAsset('images/icons/house-roof-' . $icons[$type] . '.svg'),
                'active'    => $house->getRoofType() == $type
            );
        }

        $this->saveHouseLastRoute($request);

        return $this->render(':House/Basics:roof-type.html.twig', array(
            'house'         => $house,
            'options'       => $options,
            'content'       => $this->getContentBySlug(Content::ONE_ROOF),
        ));
    }

    public function surfaceAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        if ($request->isMethod('post')) {
            $surface = $request->get('size');
            if ($surface == 'custom-input') {
                $house->setSurfaceLivingArea($request->get('square-meters'));
            } else {
                $house->setSurfaceLivingArea(null);
                $house->setSize($request->get('size'));
            }

            $this->getHouseService()->saveHouse($house, true);

            return $this->redirect($this->generateUrl('house_ownership'));
        }

        $sizes = House::getSizes();
        $options = array();
        foreach ($sizes as $type => $label) {
            $options[] = array(
                'value'     => $type,
                'label'     => $label,
                'icon'      => $this->getAsset('images/icons/house-' . $type . '.svg'),
                'active'    => !$house->getSurfaceFloor(false) && $house->getSize() == $type
            );
        }

        $this->saveHouseLastRoute($request);

        return $this->render(':House/Basics:surface.html.twig', array(
            'house'         => $house,
            'options'       => $options,
            'content'       => $this->getContentBySlug(Content::ONE_SURFACE),
        ));
    }

    public function ownershipAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        if ($request->isMethod('post')) {
            if ($house->getOwnership() != $request->get('ownership')) {
                $house->setOwnership($request->get('ownership'));
                $this->getHouseService()->saveHouse($house, true);
            }

            return $this->redirect($this->generateUrl('house_occupants'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render(':House/Basics:ownership.html.twig', array(
            'house'         => $house,
            'owner'         => House::OWNERSHIP_OWNER,
            'renter'        => House::OWNERSHIP_RENTER,
            'content'       => $this->getContentBySlug(Content::ONE_OWNER, $house->getCity()),
        ));
    }

    public function occupantsAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        if ($request->isMethod('post')) {
            if ($house->getOccupants() != $request->get('occupants')) {
                $house->setOccupants($request->get('occupants'));
                $this->getHouseService()->saveHouse($house, true);
            }

            return $this->redirect($this->generateUrl('house_energy'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render(':House/Basics:occupants.html.twig', array(
            'house'         => $house,
            'content'       => $this->getContentBySlug(Content::ONE_OCCUPANTS),
        ));
    }

    public function energyAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        if ($request->isMethod('post')) {
            $energyToUse = $request->get('energy');
            $electricHeating = (bool)$request->get('electric-heating', false);

            if ($energyToUse == 'custom') {
                $house->setConsumptionGas($request->get('gas'));
                $house->setConsumptionElec($request->get('elec'));
                $house->setConsumptionOil($request->get('oil'));
            } else {
                $house->setConsumptionGas(null);
                $house->setConsumptionElec(null);
                $house->setConsumptionOil(null);
            }

            $reset = $house->hasElectricHeating() != $electricHeating;
            $house->setElectricHeating($electricHeating);

            $this->getHouseService()->saveHouse($house, $reset);

            return $this->redirect($this->generateUrl('house_config_roof'));
        }

        $defaultsService = $this->get('one.check_je_huis.service.defaults');
        $electricHeating = $defaultsService->getEnergy($house->getBuildingType(), $house->getSize(), $house->getYear())->getElectricHeating();

        $energy = array(
            'non-elec' => array(
                'gas' => $house->getDefaultEnergy()->getGas(),
                'elec' => $house->getDefaultEnergy()->getElectricity(),
                'oil' => $house->getDefaultEnergy()->getOil(),
            ),
            'elec' => array(
                'gas' => 0,
                'elec' => $house->getDefaultEnergy()->getElectricity() + $electricHeating,
                'oil' => 0,
            ),
        );

        $this->saveHouseLastRoute($request);

        return $this->render(':House/Basics:energy.html.twig', array(
            'house'             => $house,
            'energy'            => $energy,
            'content_avg'       => $this->getContentBySlug(Content::ONE_ENERGY_AVG),
            'content_custom'    => $this->getContentBySlug(Content::ONE_ENERGY_CUSTOM),
        ));
    }

    /**
     * ZO WOON IK
     */
    public function roofConfigAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $category = $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_ROOF);

        if ($request->isMethod('post')) {
            $house->addConfig(
                $this->configRepository->getConfig($request->get('roof'))
            );

            if ($house->getRoofType() == House::ROOF_TYPE_MIXED) {
                $house->setExtraConfigRoof(
                    $this->configRepository->getConfig($request->get('roof-extra'))
                );
            } else {
                $house->setExtraConfigRoof(null);
            }

            $this->getHouseService()->saveHouse($house);

            return $this->redirect($this->generateUrl('house_config_facade'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render(':House/Config:roof.html.twig', array(
            'house'             => $house,
            'category'          => $category,
            'configBad'         => $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_FACADE),
            'configModerate'    => $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_WINDOWS),
            'configGood'        => $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_VENTILATION),
            'content'           => $this->getContentBySlug(Content::TWO_ROOF, $house->getCity()),
            'contentHeatMap'    => $this->getContentBySlug(Content::TWO_HEAT_MAP, $house->getCity()),
            'urlHeatMap'        => $house->getCity()->getThermographicPhotoLink(),
        ));
    }

    public function facadeConfigAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $category = $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_FACADE);

        if ($request->isMethod('post')) {
            $house->addConfig(
                $this->configRepository->getConfig($request->get('facade'))
            );

            $this->getHouseService()->saveHouse($house);

            return $this->redirect($this->generateUrl('house_config_floor'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render(':House/Config:facade.html.twig', array(
            'house'         => $house,
            'category'      => $category,
            'content'       => $this->getContentBySlug(Content::TWO_FACADE, $house->getCity()),
        ));
    }

    public function floorConfigAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $category = $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_FLOOR);

        if ($request->isMethod('post')) {
            $house->addConfig(
                $this->configRepository->getConfig($request->get('floor'))
            );

            $this->getHouseService()->saveHouse($house);

            return $this->redirect($this->generateUrl('house_config_window'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render(':House/Config:floor.html.twig', array(
            'house'             => $house,
            'category'          => $category,
            'content'           => $this->getContentBySlug(Content::TWO_FLOOR, $house->getCity()),
        ));
    }

    public function windowConfigAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $category = $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_WINDOWS);

        if ($request->isMethod('post')) {
            $house->addConfig(
                $this->configRepository->getConfig($request->get('window'))
            );

            $this->getHouseService()->saveHouse($house);

            return $this->redirect($this->generateUrl('house_config_ventilation'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render(':House/Config:window.html.twig', array(
            'house'         => $house,
            'category'      => $category,
            'content'       => $this->getContentBySlug(Content::TWO_WINDOW, $house->getCity()),
        ));
    }

    public function ventilationConfigAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $category = $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_VENTILATION);

        if ($request->isMethod('post')) {
            $house->addConfig(
                $this->configRepository->getConfig($request->get('ventilation'))
            );

            $this->getHouseService()->saveHouse($house);

            return $this->redirect($this->generateUrl('house_config_heating'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render(':House/Config:ventilation.html.twig', array(
            'house'         => $house,
            'category'      => $category,
            'content'       => $this->getContentBySlug(Content::TWO_VENTILATION, $house->getCity()),
        ));
    }

    public function heatingConfigAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $slug = ($house->hasElectricHeating()) ? ConfigCategory::CAT_HEATING_ELEC: ConfigCategory::CAT_HEATING;
        $category = $this->configCategoryRepository->getCategoryBySlug($slug);

        if ($request->isMethod('post')) {
            $house->addConfig(
                $this->configRepository->getConfig($request->get('heating'))
            );

            $this->getHouseService()->saveHouse($house);

            return $this->redirect($this->generateUrl('house_config_renewable'));
        }

        $this->saveHouseLastRoute($request);

        return $this->render(':House/Config:heating.html.twig', array(
            'house'         => $house,
            'category'      => $category,
            'content'       => $this->getContentBySlug(Content::TWO_HEATING, $house->getCity()),
        ));
    }

    public function renewableConfigAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $this->saveHouseLastRoute($request);

        $parameterService = $this->container->get('one.check_je_huis.service.parameter');
        $solarPanel['defaults'] = [
            'surface' => $parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_SURFACE)->getValue(),
            'count' => 0,
            'peak' => 0,
        ];

        return $this->render(':House/Config:renewable.html.twig', array(
            'house' => $house,
            'renewables' => $this->renewableRepository->getAll(),
            'content' => $this->getContentBySlug(Content::TWO_RENEWABLE, $house->getCity()),
            'solarPanel' => $solarPanel,
        ));
    }

    public function toggleRenewableConfigAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array(
            'active' => false
        );
        /** @var HouseService $houseService */
        $houseService = $this->get('one.check_je_huis.service.house');
        $house = $houseService->loadHouse();
        if ($house) {
            $renewable = $this->renewableRepository->getRenewable($request->get('renewable'));
            if ($renewable) {
                if ($house->hasRenewable($renewable)) {
                    $house->removeRenewable($renewable);
                } else {
                    $house->addRenewable($renewable);
                    $data['active'] = true;
                }

                $houseService->saveHouse($house);
            } else {
                $success = false;
                $errors[] = 'renewable not found';
            }
        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    public function energySummaryAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $this->saveHouseLastRoute($request);

        $factory = $this->get('one.check_je_huis.calculator.factory');
        $params = $factory->createParameters();
        $view = $factory->createCalculatorView($house, true);

        return $this->render(':House/Config:energy-summary.html.twig', array(
            'house'         => $house,
            'calculator'    => $view,
            'params'        => $params,
            'content'       => $this->getContentBySlug(Content::TWO_ENERGY_SUMMARY, $house->getCity()),
        ));
    }

    /**
     * ZO WIL IK WONEN
     */


    public function calculatorAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $solarPanelInfo = $this->solarPanelCalculatorService->getSolarPanelInfo($house);
        $parameterService = $this->container->get('one.check_je_huis.service.parameter');
        $solarPanelInfo['defaults'] = [
            'surface' => $parameterService->getParameterBySlug(Parameter::PARAM_SOLAR_SURFACE)->getValue(),
            'count' => 0,
            'peak' => 0,
        ];

        $this->saveHouseLastRoute($request);

        $view = $this->get('one.check_je_huis.calculator.factory')->createCalculatorView($house);

        $categories = $this->configRepository->getAllCategoriesForHouse($house);
        $categoryContent = array();
        $categoryContent[ConfigCategory::CAT_ROOF] = $this->getContentBySlug(Content::THREE_ROOF, $house->getCity());
        $categoryContent[ConfigCategory::CAT_FACADE] = $this->getContentBySlug(Content::THREE_FACADE, $house->getCity());
        $categoryContent[ConfigCategory::CAT_FLOOR] = $this->getContentBySlug(Content::THREE_FLOOR, $house->getCity());
        $categoryContent[ConfigCategory::CAT_WINDOWS] = $this->getContentBySlug(Content::THREE_WINDOW, $house->getCity());
        $categoryContent[ConfigCategory::CAT_VENTILATION] = $this->getContentBySlug(Content::THREE_VENTILATION, $house->getCity());
        $categoryContent[ConfigCategory::CAT_HEATING] = $this->getContentBySlug(Content::THREE_HEATING, $house->getCity());
        $categoryContent[ConfigCategory::CAT_HEATING_ELEC] = $categoryContent[ConfigCategory::CAT_HEATING];

        $categoryPremium = array();
        $categoryPremium[ConfigCategory::CAT_ROOF] = $this->getContentBySlug(Content::PREMIUM_ROOF, $house->getCity());
        $categoryPremium[ConfigCategory::CAT_FACADE] = $this->getContentBySlug(Content::PREMIUM_FACADE, $house->getCity());
        $categoryPremium[ConfigCategory::CAT_FLOOR] = $this->getContentBySlug(Content::PREMIUM_FLOOR, $house->getCity());
        $categoryPremium[ConfigCategory::CAT_WINDOWS] = $this->getContentBySlug(Content::PREMIUM_WINDOW, $house->getCity());
        $categoryPremium[ConfigCategory::CAT_VENTILATION] = $this->getContentBySlug(Content::PREMIUM_VENTILATION, $house->getCity());
        $categoryPremium[ConfigCategory::CAT_HEATING] = $this->getContentBySlug(Content::PREMIUM_HEATING, $house->getCity());
        $categoryPremium[ConfigCategory::CAT_HEATING_ELEC] = $categoryPremium[ConfigCategory::CAT_HEATING];

        $renewableContent = $this->getContentBySlug(Content::THREE_RENEWABLE, $house->getCity());
        $renewablePremium = $this->getContentBySlug(Content::PREMIUM_RENEWABLES, $house->getCity());

        return $this->render(':House:calculator.html.twig', array(
            'house' => $house,
            'calculator' => $view,
            'configCategories' => $categories,
            'categoryContent' => $categoryContent,
            'categoryPremium' => $categoryPremium,
            'renewables' => $this->renewableRepository->getAll(),
            'renewableContent' => $renewableContent,
            'renewablePremium' => $renewablePremium,
            'modalHeatpumpNotAllowed' => $this->getContentBySlug(Content::HEAT_PUMP_NOT_ALLOWED),
            'showDetails' => $this->get('service_container')->getParameter('calculation_debug_show'),
            'urlSolarMap' => $house->getCity()->getSunPotentialMapLink(),
            'solarMapInfo' => $this->getContentBySlug(Content::SOLAR_MAP_INFO),
            'energyLoanContent' => $this->getContentBySlug(Content::INFO_ENERGY_LOAN, $house->getCity()),
            'solarPanelInfo' => $solarPanelInfo,
        ));
    }

    public function calculationDetailAction()
    {
        $house = $this->getSessionHouse();
        if (!$house) {
            return $this->noHouseRedirect();
        }

        $view = $this->get('one.check_je_huis.calculator.factory')->createCalculatorView($house);

        return $this->render(':House:calculation-detail.html.twig', array(
            'house'             => $house,
            'calculator'        => $view,
        ));
    }

    /**
     * AJAX CALLS
     */

    public function updateSingleConfigAction(Request $request)
    {
        $house = $this->getSessionHouse();
        if (!$house) {
            return $this->noHouseRedirect();
        }

        $success = true;
        $errors = array();
        $data = array();

        $configId   = (int)$request->get('config');
        $categoryId = (int)$request->get('category');
        $options    = $request->get('options');
        $category   = null;
        $config     = null;

        /** @var HouseService $houseService */
        $houseService = $this->get('one.check_je_huis.service.house');
        if ($house) {
            $category = $this->configCategoryRepository->find($categoryId);

            // if $config == 0 we need to remove the configuration for the given category
            if (!$configId) {
                if (!$category) {
                    $success = false;
                    $errors[] = 'invalid category id given';
                } else {

                    // get the selected upgrade config and remove it
                    $config = $house->getUpgradeConfig($category);
                    if ($config) {
                        if ($options === 'extra' && $category->getSlug() === ConfigCategory::CAT_ROOF) {
                            $house->setExtraUpgradeRoof(null);
                        } else {
                            // if we are removing attic floor insulation, reset any custom surface area
                            if ($config->getId() !== Config::CONFIG_ATTIC_FLOOR &&
                                $config->getCategory()->getSlug() === ConfigCategory::CAT_ROOF &&
                                $house->getUpgradeConfig($config->getCategory()) === Config::CONFIG_ATTIC_FLOOR
                            ) {
                                $house->setSurfaceRoof(null);
                            }
                            $house->removeUpgradeConfig($config);
                        }
                    }

                    $houseService->saveHouse($house);
                }
            } else {
                $config = $this->configRepository->getConfig($configId);

                // is the config from the correct category?
                if ($config && $categoryId === $config->getCategory()->getId()) {
                    // do we need to set the extra roof config?
                    if ($options === 'extra' && $category->getSlug() === ConfigCategory::CAT_ROOF) {
                        if ($config === $house->getExtraConfigRoof()) {
                            $house->setExtraUpgradeRoof(null);
                        } else {
                            $house->setExtraUpgradeRoof($config);
                        }
                    } else {
                        $currentConfig = $house->getUpgradeConfig($category);

                        // the config can't be the current config...
                        if (!$currentConfig || $house->getConfig($category) !== $config) {

                            // if we are changing away from or to attic floor insulation, reset any custom surface area
                            if ($config->isCategory(ConfigCategory::CAT_ROOF) &&
                                (($config->getId() !== Config::CONFIG_ATTIC_FLOOR && $currentConfig && $currentConfig->getId() === Config::CONFIG_ATTIC_FLOOR) ||
                                ($config->getId() === Config::CONFIG_ATTIC_FLOOR && (!$currentConfig || $currentConfig->getId() !== Config::CONFIG_ATTIC_FLOOR)))
                            ) {
                                $house->setSurfaceRoof(null);
                            }
                            $house->addUpgradeConfig($config);
                        } else {
                            if ($currentConfig) {
                                // if we are changing away from or to attic floor insulation, reset any custom surface area
                                if ($config->getCategory()->getSlug() === ConfigCategory::CAT_ROOF &&
                                    (($config->getId() !== Config::CONFIG_ATTIC_FLOOR && $currentConfig && $currentConfig->getId() === Config::CONFIG_ATTIC_FLOOR) ||
                                    ($config->getId() === Config::CONFIG_ATTIC_FLOOR && (!$currentConfig || $currentConfig->getId() !== Config::CONFIG_ATTIC_FLOOR)))
                                ) {
                                    $house->setSurfaceRoof(null);
                                }
                                $house->removeUpgradeConfig($currentConfig);
                            }
                        }
                    }

                    $houseService->saveHouse($house);
                } else {
                    $success = false;
                    $errors[] = 'invalid config id given';
                }
            }
        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        if ($success) {
            $data = $this->getUpdatedCalculatorInfo($house, $data);
            $data['good'] = $config && $config->isPossibleUpgrade();
        }

        return new JsonResponse(array(
            'success'   => $success,
            'errors'    => $errors,
            'data'      => $data,
        ));
    }

    public function toggleRenewableAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array(
            'active' => false
        );

        /** @var HouseService $houseService */
        $houseService = $this->get('one.check_je_huis.service.house');
        $house = $houseService->loadHouse();
        if ($house) {
            $renewable = $this->renewableRepository->getRenewable($request->get('renewable'));
            if ($renewable) {
                if ($house->hasUpgradeRenewable($renewable)) {
                    $house->removeUpgradeRenewable($renewable);
                } else {
                    $house->addUpgradeRenewable($renewable);
                    $data['active'] = true;
                }

                $houseService->saveHouse($house);
            } else {
                $success = false;
                $errors[] = 'renewable not found';
            }
        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    public function toggleWindroofAction($current = false)
    {
        $success = true;
        $errors = array();
        $data = array(
            'active' => false
        );

        /** @var HouseService $houseService */
        $houseService = $this->get('one.check_je_huis.service.house');
        $house = $houseService->loadHouse();
        if ($house) {
            if ($current) {
                $house->setHasWindRoof(!$house->hasWindRoof());
                $data['active'] = $house->hasWindRoof();
            } else {
                $house->setPlaceWindroof(!$house->getPlaceWindroof());
                $data['active'] = $house->getPlaceWindroof();
            }
            $houseService->saveHouse($house);
        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    public function updateSurfaceRoofAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array();

        /** @var HouseService $houseService */
        $houseService = $this->get('one.check_je_huis.service.house');
        $house = $houseService->loadHouse();
        if ($house) {
            $surface = $request->get('surface');
            if (is_numeric($surface)) {
                $house->setSurfaceRoof((float)$surface);
            } else {
                $house->setSurfaceRoof(null);
            }
            $houseService->saveHouse($house);
        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    public function updateSurfaceRoofExtraAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array();

        /** @var HouseService $houseService */
        $houseService = $this->get('one.check_je_huis.service.house');
        $house = $houseService->loadHouse();
        if ($house) {
            $surface = $request->get('surface');
            if ($house->getRoofType() == House::ROOF_TYPE_MIXED && is_numeric($surface)) {
                $house->setSurfaceRoofExtra((float)$surface);
            } else {
                $house->setSurfaceRoofExtra(null);
            }
            $houseService->saveHouse($house);
        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    public function updateSurfaceFloorAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array();

        /** @var HouseService $houseService */
        $houseService = $this->get('one.check_je_huis.service.house');
        $house = $houseService->loadHouse();
        if ($house) {
            $surface = $request->get('surface');
            if (is_numeric($surface)) {
                $house->setSurfaceFloor((float)$surface);
            } else {
                $house->setSurfaceFloor(null);
            }
            $houseService->saveHouse($house);
        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    public function updateSurfaceFacadeAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array();

        /** @var HouseService $houseService */
        $houseService = $this->get('one.check_je_huis.service.house');
        $house = $houseService->loadHouse();
        if ($house) {
            $surface = $request->get('surface');
            if (is_numeric($surface)) {
                $house->setSurfaceFacade((float)$surface);
            } else {
                $house->setSurfaceFacade(null);
            }
            $houseService->saveHouse($house);
        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    public function updateSurfaceWindowAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array();

        /** @var HouseService $houseService */
        $houseService = $this->get('one.check_je_huis.service.house');
        $house = $houseService->loadHouse();
        if ($house) {
            $surface = $request->get('surface');
            if (is_numeric($surface)) {
                $house->setSurfaceWindow((float)$surface);
            } else {
                $house->setSurfaceWindow(null);
            }
            $houseService->saveHouse($house);
        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data
            )
        );
    }

    public function updateSolarWPAction(Request $request)
    {
        $success = true;
        $errors = array();
        $data = array();

        /** @var HouseService $houseService */
        $houseService = $this->get('one.check_je_huis.service.house');
        $house = $houseService->loadHouse();
        $solarpanelInfo = [];
        if ($house) {
            $name = $request->query->get('name');
            $value = $request->query->get('value');
            if (is_numeric($value)) {
                $house->setSolarPanelParams($name, $value);
                $solarpanelInfo = $this->solarPanelCalculatorService->calculate($name, $value);
            }
            $houseService->saveHouse($house);
        } else {
            $success = false;
            $errors[] = 'no house found in session';
        }

        $data = $this->getUpdatedCalculatorInfo($house, $data);

        return new JsonResponse(
            array(
                'success'   => $success,
                'errors'    => $errors,
                'data'      => $data,
                'solarpanelInfo' => $solarpanelInfo,
            )
        );
    }

    /**
     * HOUSE TOKEN, PDF GENERATION AND MAILS
     */

    public function loadHouseFromTokenAction($token)
    {
        $isLoaded = $this->getHouseService()->loadHouseFromToken($token);

        // if we have a house, try redirect to the last know route
        if ($isLoaded) {
            $house = $this->getSessionHouse();
            if ($house) {
                $lastRoute = $house->getLastKnownRoute();
                $router = $this->get('router');

                // do we have a valid route?
                if ($lastRoute && $router->getRouteCollection()->get($lastRoute)) {
                    return $this->redirect($this->generateUrl($lastRoute));
                }
            }
        }

        return $this->render(':House:load-from-token.html.twig', array(
            'loaded' => $isLoaded
        ));
    }

    public function mailTokenUrlAction(Request $request)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $form = $this->createForm(new HouseEmailType(), $house);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $mailer = $this->get('one.check_je_huis.service.mailer');
                $mailer->mailHouseToken($house);
            }
        }

        return $this->render(':House:mail-token.html.twig', array(
            'house' => $house,
            'host'  => $_SERVER['HTTP_HOST'],
            'form' => $form->createView()
        ));
    }

    public function calculationPdfTemplateAction($token)
    {
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }
        /** @var HouseService $houseService */
        $houseService = $this->get('one.check_je_huis.service.house');
        $houseService->loadHouseFromToken($token);
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->render(':Pdf:error.html.twig');
        }

        $view = $this->get('one.check_je_huis.calculator.factory')->createCalculatorView($house);
        $subsidyService = $this->get('one.check_je_huis.service.subsidy');

        return $this->render(':Pdf:plan.html.twig', array(
            'house'             => $house,
            'calculator'        => $view,
            'configCategories'  => $this->configRepository->getAllCategories(),
            'renewables'        => $this->renewableRepository->getAll(),
            'subsidies'         => iterator_to_array($this->idsAsKeys($subsidyService->getAllSubsidyCategories())),
            'pdf_extra_content' => $this->getContentBySlug(Content::PDF_EXTRA_CONTENT, $house->getCity()),
        ));
    }

    /**
     * @param $entities
     * @return \Generator|array
     */
    protected function idsAsKeys($entities)
    {
        foreach ($entities as $e) {
            yield $e->getId() => $e;
        }
    }

    public function calculationPdfAction()
    {
        $house = $this->getSessionHouse();
        if (!$house) {
            return $this->noHouseRedirect();
        }

        try {

            // disable toolbar when displaying the pdf, even in dev
            error_log((int)$this->container->has('profiler'));
            if ($this->container->has('profiler')) {
                $this->container->get('profiler')->disable();
            }

            return new Response(
                $this->getHouseService()->generatePdf($house),
                200,
                array(
                    'Content-Type'          => 'application/pdf',
                    // uncomment to download pdf instead of view it in browser
                    // 'Content-Disposition'   => 'attachment; filename="check-je-huis.pdf"'
                )
            );
        } catch (\Exception $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
        }

        return $this->render(':Pdf:error.html.twig');
    }

    public function mailPdfAction(Request $request)
    {
        $house = $this->getSessionHouse();
        if (!$house) {
            return $this->noHouseRedirect();
        }

        $download = false;
        if ($request->getMethod() == 'POST') {
            $form = $this->createForm(new MailPlanType(), $house);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->getHouseService()->saveHouse($house);

                try {
                    $pdf = $this->getHouseService()->generatePdf($house);
                    if ($house->getEmail()) {
                        $mailer = $this->get('one.check_je_huis.service.mailer');
                        $mailer->mailCalculatorPdf(
                            $house,
                            $pdf
                        );
                        $request->getSession()->getFlashBag()->add('success', 'Het stappenplan is verstuurd.');
                    }
                } catch (\Exception $e) {
                    error_log($e->getMessage());
                    error_log($e->getTraceAsString());

                    return $this->render(':Pdf:error.html.twig');
                }
            }
        }

        return $this->redirect($this->generateUrl('app_plan', array('download' => $download)));
    }

    public function downloadPdfAction()
    {
        $house = $this->getSessionHouse();
        if (!$house) {
            return $this->noHouseRedirect();
        }

        $pdf = $this->getHouseService()->generatePdf($house);

        return new Response($pdf, 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('attachment; filename="%s"',
                'mijn-warm-huis-stappenplan.pdf')
        ));
    }

    public function pollAction()
    {
        $house = $this->getSessionHouse();
        if (!$house) {
            return $this->noHouseRedirect();
        }

        $return = array('success' => true);

        $return['solar_panels'] = $house->getSolarPanelsSurface();

        return new JsonResponse($return);
    }

    /**
     * ADMIN HOUSE LIST
     */

    public function adminListAction(Request $request)
    {
        $houseService = $this->get('one.check_je_huis.service.house');

        $filter = $this->getAdminHouseFilter($request);
        $city = null;
        if (!$this->getUser()->isAdmin()) {
            $city = $this->getUser()->getCity();
        }
        $houses = $houseService->getAllHouses($filter, $city);

        return $this->render(':House:admin-list.html.twig', array(
            'houses' => $houses,
            'filter' => $filter,
        ));
    }

    public function adminListExportAction(Request $request)
    {
        $houseService = $this->get('one.check_je_huis.service.house');
        $filter = $this->getAdminHouseFilter($request);
        $city = null;
        if (!$this->getUser()->isAdmin()) {
            $city = $this->getUser()->getCity();
        }
        $houses = $houseService->getAllHouses($filter, $city);

        $buildingTypes = House::getBuildingTypes();
        $roofTypes = House::getRoofTypes();
        $sizes = House::getSizes();
        $ownerships = House::getOwnerships();
        $years = House::getYears();

        $catRoof = $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_ROOF);
        $catFacade = $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_FACADE);
        $catFloor = $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_FLOOR);
        $catWindows = $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_WINDOWS);
        $catVent = $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_VENTILATION);
        $catHeating = $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_HEATING);
        $catHeatingElec = $this->configCategoryRepository->getCategoryBySlug(ConfigCategory::CAT_HEATING_ELEC);

        $solarWater = $this->renewableRepository->getRenewableBySlug(Renewable::RENEWABLE_SOLAR_WATER_HEATER);
        $solarPanels = $this->renewableRepository->getRenewableBySlug(Renewable::RENEWABLE_SOLAR_PANELS);
        $greenPower = $this->renewableRepository->getRenewableBySlug(Renewable::RENEWABLE_GREEN_POWER);

        $csv = 'sep=,' . PHP_EOL;
        $csv .= implode(',', array(
                'id',
                'token',
                'laatste update',
                'NIS nummer',
                'email',
                'nieuwsbrief',
                'adres',
                'type gebouw',
                'bouwjaar',
                'type dak',
                'grootte',
                'BVO',
                'eigenaar',
                'bewoners',
                'verbruik gas',
                'verbruik elektriciteit',
                'verbruik stookolie',
                'verwarming electrisch',
                'dakisolatie',
                'dakisolatie 2',
                'winddicht onderdak',
                'gevelisolatie',
                'vloerisolatie',
                'ramen',
                'ventilatie',
                'verwarming',
                'zonneboiler',
                'PV cellen',
                'PV cellen m²',
                'groene stroom',
                'gewenste dakisolatie',
                'winddicht onderdak gewenst',
                'opp. dakisolatie',
                'gewenste dakisolatie 2',
                'opp. dakisolatie plat',
                'gewenste gevelisolatie',
                'opp. gevelisolatie',
                'gewenste vloerisolatie',
                'opp. vloerisolatie',
                'gewenste ramen',
                'opp. ramen vernieuwen',
                'gewenste ventilatie',
                'gewenste verwarming',
                'gewenste zonneboiler',
                'gewenste PV cellen',
                'gewenste groene stroom',
                'huidige verbruik gas',
                'huidige verbruik electriciteit',
                'huidige verbruik stookolie',
                'huidige verbruik per m²',
                'verbruik gas na renovatie',
                'verbruik electriciteit na renovatie',
                'verbruik stookolie na renovatie',
                'verbruik na renovatie per m²',
            )) . PHP_EOL;

        foreach ($houses as $house) {
            $view = $this->container->get('one.check_je_huis.calculator.factory')->createCalculatorView($house);
            $email = $house->getEmail(true);
            if ($email && (int) $house->getNewsletter() !== 1) {
                // GDPR: don't export email if newsletter isn't checked
                $email = House::EMAIL_ANONYMOUS;
            }
            $csv .= '"' . implode('","', array(
                    $house->getId(),
                    $house->getToken(),
                    $house->getLastUpdate()->format('Y-m-d H:i:s'),
                    $house->getCity()->getNisNumber(),
                    $email,
                    $house->getNewsletter() ? 1: 0,
                    $house->getAddress(),
                    $buildingTypes[$house->getBuildingType()],
                    $years[$house->getYear()],
                    $roofTypes[$house->getRoofType()],
                    $sizes[$house->getSize()],
                    $house->getSurfaceLivingArea(),
                    $ownerships[$house->getOwnership()],
                    $house->getOccupants(),
                    $house->getConsumptionGas(),
                    $house->getConsumptionElec(),
                    $house->getConsumptionOil(),
                    $house->hasElectricHeating() ? 1: 0,
                    $house->getConfig($catRoof)->getLabel(),
                    $house->getExtraConfigRoof() ? $house->getExtraConfigRoof()->getLabel(): '',
                    $house->hasWindRoof() ? 1: 0,
                    $house->getConfig($catFacade)->getLabel(),
                    $house->getConfig($catFloor)->getLabel(),
                    $house->getConfig($catWindows)->getLabel(),
                    $house->getConfig($catVent)->getLabel(),
                    $house->hasElectricHeating() ?
                        $house->getConfig($catHeatingElec)->getLabel():
                        $house->getConfig($catHeating)->getLabel(),
                    $house->hasRenewable($solarWater) ? 1: 0,
                    $house->hasRenewable($solarPanels) ? 1: 0,
                    $house->getSolarPanelsSurface($solarPanels),
                    $house->hasRenewable($greenPower) ? 1: 0,
                    $house->getUpgradeConfig($catRoof) ? $house->getUpgradeConfig($catRoof)->getLabel(): '',
                    $house->getSurfaceRoof(),
                    $house->getExtraUpgradeRoof() ? $house->getExtraUpgradeRoof()->getLabel(): '',
                    $house->getSurfaceRoofExtra(),
                    $house->getPlaceWindroof() ? 1: 0,
                    $this->getUpgradeConfigLabel($house, $catFacade),
                    $house->getSurfaceFacade(),
                    $this->getUpgradeConfigLabel($house, $catFloor),
                    $house->getSurfaceFloor(),
                    $this->getUpgradeConfigLabel($house, $catWindows),
                    $house->getSurfaceWindow(),
                    $this->getUpgradeConfigLabel($house, $catVent),
                    $house->hasElectricHeating() ?
                        $this->getUpgradeConfigLabel($house, $catHeatingElec):
                        $this->getUpgradeConfigLabel($house, $catHeating),
                    $house->hasUpgradeRenewable($solarWater) ? 1: 0,
                    $house->hasUpgradeRenewable($solarPanels) ? 1: 0,
                    $house->hasUpgradeRenewable($greenPower) ? 1: 0,
                    $view->getCurrent()->getState()->getGas(),
                    $view->getCurrent()->getState()->getElectricity(),
                    $view->getCurrent()->getState()->getOil(),
                    $view->getAvgScore(true),
                    $view->getUpgrade()->getState()->getGas(),
                    $view->getUpgrade()->getState()->getElectricity(),
                    $view->getUpgrade()->getState()->getOil(),
                    $view->getAvgScore(),
                )) . '"' . PHP_EOL;
        }

        $event = new HousesExportEvent($filter);
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch('housesExport', $event);

        $response = new Response(
            $csv,
            200,
            array(
                'Content-Type'              => 'text/csv',
                'Content-Description'       => 'Mijn Warm Huis - export',
                'Content-Disposition'       => 'attachment; filename=klimaatstad-beslissingsboom-export.csv',
                'Pragma'                    => 'no-cache',
                'Expires'                   => '0',
            )
        );

        return $response;
    }

    protected function getUpgradeConfigLabel(House $house, $category)
    {
        if ($house->getUpgradeConfig($category)) {
            return $house->getUpgradeConfig($category)->getLabel();
        }

        return $house->getConfig($category)->getLabel();
    }

    protected function getAdminHouseFilter(Request $request)
    {
        $filter = $request->get('table-filter', array());

        $filter['from'] = (isset($filter['from'])) ?
            new \Datetime(date(\Datetime::ISO8601, $filter['from']/1000)):
            new \Datetime();
        $filter['from']->setTime(0, 0, 0);

        $filter['to'] = (isset($filter['to'])) ?
            new \Datetime(date(\Datetime::ISO8601, $filter['to']/1000)):
            new \Datetime();
        $filter['to']->setTime(23, 59, 59);

        return $filter;
    }

    /**
     * HELP FUNCTIONS
     */

    protected function saveHouseLastRoute(Request $request)
    {
        $house = $this->getSessionHouse();
        $currentRout = $request->get('_route');
        $house->setLastKnownRoute($currentRout);
        $this->getHouseService()->saveHouse($house);
    }

    protected function getCalculatorView(House $house)
    {
        if (!$this->calculatorView) {
            $this->calculatorView = $this->get('one.check_je_huis.calculator.factory')->createCalculatorView($house);
        }

        return $this->calculatorView;
    }

    protected function getUpdatedCalculatorInfo(House $house, array $data = array())
    {
        $view = $this->getCalculatorView($house);

        // totals

        $data['renewable_diff'] = Format::energy($view->getEnergyDiffForRenewables());
        $data['renewable_price'] = Format::price($view->getPriceDiffForRenewables());
        $data['energy_diff'] = Format::energy($view->getEnergyDiff());
        $data['price_diff'] = Format::price($view->getPriceDiff());
        $data['subsidies'] = Format::price($view->getSubsidyTotal());
        $data['cost'] = Format::price($view->getBuildCostTotal());
        $data['energy_loan'] = Format::price($view->getEnergyLoanTotal());
        $data['co2'] = Format::CO2($view->getCo2Diff());
        $data['score_config'] = $view->getAvgScoreConfig();

        // roof settings and surfaces
        $data['roof_windroof_possible'] = $house->canHaveWindRoof();
        $data['roof_surface'] = $house->getSurfaceRoof(true, $house->getUpgradeConfig(ConfigCategory::CAT_ROOF));

        // heat pump allowed?
        $data['heat_pump_allowed'] = $house->isHeatPumpAllowed();

        // category specific

        foreach ($house->getConfigs() as $config) {
            $data['categories'][$config->getCategory()->getSlug()] = array(
                'diff'  => Format::energy(
                    $view->getEnergyDiffForCategory($config->getCategory())
                ),
                'price' => Format::price(
                    $view->getPriceDiffForCategory($config->getCategory())
                ),
            );
        }

        // renewables

        $renewables = array(
            'diff' => 0,
            'price' => 0,
        );
        foreach ($house->getUpgradeRenewables() as $renewable) {
            $renewables['diff'] += $view->getEnergyDiffForRenewable($renewable);
            $renewables['price'] += $view->getPriceDiffForRenewable($renewable);
        }
        $renewables['diff'] = Format::energy($renewables['diff']);
        $renewables['price'] = Format::price($renewables['price']);
        $data['renewables'] = $renewables;

        // changing extra info
        $data['solarpanels'] = [
            'solar_surface' => $house->getSolarPanelsSurface(),
            'solar_count' => $house->getSolarPanelCount(),
            'solar_peak' => $house->getSolarPanelPeak()
        ];


        return $data;
    }

    /**
     * Temporary function to reset the session house
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function resetAction(Request $request)
    {
        /** @var HouseService $houseService */
        $houseService = $this->get('one.check_je_huis.service.house');
        $house = $houseService->loadHouse();
        if ($house) {
            $houseService->getDoctrine()->remove($house);
        }

        $request->getSession()->set(HouseService::HOUSE_SESSION_KEY, null);

        return $this->redirect($this->generateUrl('app_index'));
    }

    public function exportAction(Request $request, $token)
    {
        $isLoaded = $this->getHouseService()->loadHouseFromToken($token);

        $data = [];

        if ($isLoaded) {
            $house = $this->getSessionHouse();
            $calc = $this->getCalculatorView($house);
            $current = $calc->getCurrent()->getState();
            $upgrade = $calc->getUpgrade()->getState();

            $data = [
                'type' => $house->getBuildingType(),
                'year' => (int)$house->getYear(),
                'roof' => $house->getRoofType(),
                'surface' => $house->getSurfaceLivingArea(),
                'occupants' => (int)$house->getOccupants(),
                'energy' => [
                    'start' => [
                        'elec' => $house->getConsumptionElec(),
                        'gas' => $house->getConsumptionGas(),
                        'oil' => $house->getConsumptionOil(),
                        'electric_heating' => $house->hasElectricHeating(),
                    ],
                    'current' => [
                        'elec' => $current->getElectricity(),
                        'gas' => $current->getGas(),
                        'oil' => $current->getOil(),
                        'electric_heating' => $current->isHeatingElectric(),
                    ],
                    'upgrade' => [
                        'elec' => $upgrade->getElectricity(),
                        'gas' => $upgrade->getGas(),
                        'oil' => $upgrade->getOil(),
                        'electric_heating' => $upgrade->isHeatingElectric(),
                    ],
                ],
                'solar_panels' => $house->getSolarPanelsSurface(),
                'energy_custom' => $house->hasCustomEnergy(),
                'surface_custom' => $house->hasCustomSurfaces(),
                'upgrade_details' => [
                    'energy_diff' => $calc->getEnergyDiff(),
                    'price_diff' => $calc->getPriceDiff(),
                    'co2' => $calc->getCo2Diff(),
                    'subsidies' => $calc->getSubsidyTotal(),
                    'cost' => $calc->getBuildCostTotal(),
                ],
            ];

            foreach ($house->getConfigs() as $config) {
                $upgrade = $house->getUpgradeConfig($config->getCategory());
                $data['categories'][$config->getCategory()->getSlug()] = [
                    'current' => $config->getLabel(),
                    'upgrade' => $upgrade ? $upgrade->getLabel(): null,
                    'energy_diff' => $calc->getEnergyDiffForCategory($config->getCategory()),
                    'co2_diff' => $calc->getCo2DiffForCategory($config->getCategory()),
                    'price_diff' => $calc->getPriceDiffForCategory($config->getCategory()),
                ];
            }

            foreach ($house->getAllRenewables() as $renewable) {
                $data['renewables'][$renewable->getSlug()] = [
                    'current' => $house->hasRenewable($renewable),
                    'upgrade' => $house->hasUpgradeRenewable($renewable),
                    'energy_diff' => $calc->getEnergyDiffForRenewable($renewable),
                    'co2_diff' => $calc->getCo2DiffForCategory($renewable),
                    'price_diff' => $calc->getPriceDiffForRenewable($renewable),
                ];
            }
        } else {
            $data = [
                'success' => false,
                'code' => 'HOUSE_NOT_FOUND',
                'error' => 'no house found for token',
            ];
        }

        return new JsonResponse($data);
    }
}
