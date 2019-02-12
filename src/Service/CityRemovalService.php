<?php

namespace One\CheckJeHuis\Service;

use One\CheckJeHuis\Entity\City;
use One\CheckJeHuis\Entity\House;
use One\CheckJeHuis\Entity\Subsidy;
use One\CheckJeHuis\Entity\User;
use One\CheckJeHuis\Exception\DeleteOwnCityException;
use One\CheckJeHuis\Repository\CityRepository;
use One\CheckJeHuis\Repository\ContentRepository;
use One\CheckJeHuis\Repository\HouseRepository;
use One\CheckJeHuis\Repository\SubsidyRepository;
use One\CheckJeHuis\Repository\UserRepository;

class CityRemovalService
{
    /**
     * @var CityRepository
     */
    private $cityRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var HouseRepository
     */
    private $houseRepository;
    /**
     * @var SubsidyRepository
     */
    private $subsidyRepository;
    /**
     * @var ContentRepository
     */
    private $contentRepository;

    public function __construct(
        CityRepository $cityRepository,
        UserRepository $userRepository,
        HouseRepository $houseRepository,
        SubsidyRepository $subsidyRepository,
        ContentRepository $contentRepository
    ) {
        $this->cityRepository = $cityRepository;
        $this->userRepository = $userRepository;
        $this->houseRepository = $houseRepository;
        $this->subsidyRepository = $subsidyRepository;
        $this->contentRepository = $contentRepository;
    }

    public function remove(City $city, User $user)
    {
        if ($city->getId() === $user->getCity()->getId()) {
            throw new DeleteOwnCityException();
        }

        $houses = $this->houseRepository->getHousesFromCity($city);
        /** @var  House $house */
        foreach ($houses as $house) {
            foreach ($house->getConfigs() as $config) {
                $house->removeConfig($config);
            }
            $this->houseRepository->removeHouse($house);
        }

        $subsidies = $this->subsidyRepository->getSubsidiesFromCity($city);
        /** @var  Subsidy $subsidy */
        foreach ($subsidies as $subsidy) {
            foreach ($subsidy->getConfigs() as $config) {
                $subsidy->removeConfig($config);
            }
            $this->subsidyRepository->removeSubsidy($subsidy);
        }

        $this->contentRepository->removeContentFromCity($city);
        $this->userRepository->removeUsersFromCity($city);
        $this->cityRepository->remove($city);
    }
}