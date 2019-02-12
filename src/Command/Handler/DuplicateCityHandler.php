<?php

namespace One\CheckJeHuis\Command\Handler;

use One\CheckJeHuis\Command\DuplicateCityCommand;
use One\CheckJeHuis\Entity\City;
use One\CheckJeHuis\Entity\Content;
use One\CheckJeHuis\Entity\Subsidy;
use One\CheckJeHuis\Repository\CityRepository;
use One\CheckJeHuis\Repository\ContentRepository;
use One\CheckJeHuis\Repository\SubsidyRepository;
use Symfony\Component\HttpFoundation\File\File;

class DuplicateCityHandler
{
    /**
     * @var CityRepository
     */
    private $cityRepository;

    /**
     * @var ContentRepository
     */
    private $contentRepository;

    /**
     * @var SubsidyRepository
     */
    private $subsidyRepository;

    public function __construct(
        CityRepository $cityRepository,
        ContentRepository $contentRepository,
        SubsidyRepository $subsidyRepository
    ) {
        $this->cityRepository = $cityRepository;
        $this->contentRepository = $contentRepository;
        $this->subsidyRepository = $subsidyRepository;
    }

    public function handle(DuplicateCityCommand $duplicateCityCommand)
    {
        $city = new City();
        $city->setName($duplicateCityCommand->name);
        $city->setCityName($duplicateCityCommand->cityName);
        $city->setPostalCode($duplicateCityCommand->postalCode);
        $city->setPrefillCity($duplicateCityCommand->prefillCity);
        $city->setNisNumber($duplicateCityCommand->nisNumber);
        $city->setEmail($duplicateCityCommand->email);
        $city->setUrl($duplicateCityCommand->url);
        if ($duplicateCityCommand->headerLogoImage instanceof File) {
            $city->setHeaderLogoImageFile($duplicateCityCommand->headerLogoImage);
            $city->setHeaderLogoImage($duplicateCityCommand->headerLogoImage);
        }
        if ($duplicateCityCommand->footerLogoImage instanceof File) {
            $city->setFooterLogoImageFile($duplicateCityCommand->footerLogoImage);
            $city->setFooterLogoImage($duplicateCityCommand->footerLogoImage);
        }
        $city->setFooterLogoLink($duplicateCityCommand->footerLogoLink);
        $city->setBackgroundColor($duplicateCityCommand->backgroundColor);
        $city->setBackgroundColorHeader($duplicateCityCommand->backgroundColorHeader);
        $city->setButtonColor($duplicateCityCommand->buttonColor);
        $city->setTextColor($duplicateCityCommand->textColor);
        $city->setTextColorHeader($duplicateCityCommand->textColorHeader);
        $city->setShowInDropdown($duplicateCityCommand->showInDropdown);
        $city->setShowSpecificInfo($duplicateCityCommand->showSpecificInfo);
        $city->setJavascriptBottom($duplicateCityCommand->javascriptBottom);
        $city->setThermographicPhotoLink($duplicateCityCommand->thermographicPhotoLink);
        $city->setSunPotentialMapLink($duplicateCityCommand->sunPotentialMapLink);
        if ($duplicateCityCommand->thermographicPhotoImage instanceof File) {
            $city->setThermographicPhotoImageFile($duplicateCityCommand->thermographicPhotoImage);
            $city->setThermographicPhotoImage($duplicateCityCommand->thermographicPhotoImage);
        }
        $city->setStayUpToDate($duplicateCityCommand->stayUpToDate);
        $defaults = [
            'build_year' => $duplicateCityCommand->defaultBuildYear,
            'building_type' => $duplicateCityCommand->defaultBuildingType
        ];
        $city->setDefaults($defaults);
        $this->cityRepository->add($city);

        $cityToCopyFrom = $duplicateCityCommand->city;
        $this->copyContent($cityToCopyFrom, $city);
        $this->copySubsidies($cityToCopyFrom, $city);

    }

    /**
     * @param $cityToCopyFrom
     * @param $city
     */
    private function copySubsidies($cityToCopyFrom, $city)
    {
        $subsidies = $this->subsidyRepository->findBy(['city' => $cityToCopyFrom]);
        foreach ($subsidies as $subsidy) {
            /** @var Subsidy $subsidy */
            $newSubsidy = clone $subsidy;
            $newSubsidy->setCity($city);
            $newSubsidy->setConfigs($subsidy->getConfigs());
            $this->subsidyRepository->add($newSubsidy);
        }
    }

    /**
     * @param $cityToCopyFrom
     * @param $city
     */
    private function copyContent($cityToCopyFrom, $city)
    {
        $contents = $this->contentRepository->findBy(['city' => $cityToCopyFrom]);
        foreach ($contents as $content) {
            /** @var Content $newContent */
            $newContent = clone $content;
            $newContent->setCity($city);
            $this->contentRepository->add($newContent);
        }
    }
}