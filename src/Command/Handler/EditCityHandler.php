<?php

namespace One\CheckJeHuis\Command\Handler;

use One\CheckJeHuis\Command\EditCityCommand;
use One\CheckJeHuis\Entity\City;
use One\CheckJeHuis\Repository\CityRepository;
use One\CheckJeHuis\Repository\ContentRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Handler\UploadHandler;

class EditCityHandler
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
     * @var UploadHandler
     */
    private $handler;

    public function __construct(
        CityRepository $cityRepository,
        ContentRepository $contentRepository,
        UploadHandler $handler
    ) {
        $this->cityRepository = $cityRepository;
        $this->contentRepository = $contentRepository;
        $this->handler = $handler;
    }

    public function handle(EditCityCommand $editCityCommand)
    {
        /** @var City $city */
        $city = $this->cityRepository->find($editCityCommand->id);
        $city->setName($editCityCommand->name);
        $city->setCityName($editCityCommand->cityName);
        $city->setPostalCode($editCityCommand->postalCode);
        $city->setPrefillCity($editCityCommand->prefillCity);
        $city->setNisNumber($editCityCommand->nisNumber);
        $city->setEmail($editCityCommand->email);
        $city->setUrl($editCityCommand->url);
        if ($editCityCommand->deleteHeaderLogoImage === true) {
            $this->handler->remove($city, 'headerLogoImageFile');
        }
        if ($editCityCommand->deleteFooterLogoImage === true) {
            $this->handler->remove($city, 'footerLogoImageFile');
        }
        if ($editCityCommand->deleteThermographicPhotoImage === true) {
            $this->handler->remove($city, 'thermographicPhotoImageFile');
        }
        if ($editCityCommand->headerLogoImage instanceof File) {
            $city->setHeaderLogoImageFile($editCityCommand->headerLogoImage);
            $city->setHeaderLogoImage($editCityCommand->headerLogoImage);
        }
        if ($editCityCommand->footerLogoImage instanceof File) {
            $city->setFooterLogoImageFile($editCityCommand->footerLogoImage);
            $city->setFooterLogoImage($editCityCommand->footerLogoImage);
        }
        $city->setFooterLogoLink($editCityCommand->footerLogoLink);
        $city->setBackgroundColor($editCityCommand->backgroundColor);
        $city->setBackgroundColorHeader($editCityCommand->backgroundColorHeader);
        $city->setButtonColor($editCityCommand->buttonColor);
        $city->setTextColor($editCityCommand->textColor);
        $city->setTextColorHeader($editCityCommand->textColorHeader);
        $city->setShowInDropdown($editCityCommand->showInDropdown);
        $city->setShowSpecificInfo($editCityCommand->showSpecificInfo);
        $city->setJavascriptBottom($editCityCommand->javascriptBottom);
        $city->setThermographicPhotoLink($editCityCommand->thermographicPhotoLink);
        $city->setSunPotentialMapLink($editCityCommand->sunPotentialMapLink);
        $cityDefaults = $city->getDefaults();
        $defaults = [
            'build_year' => $editCityCommand->defaultBuildYear,
            'building_type' => $editCityCommand->defaultBuildingType
        ];
        $defaults = array_merge($cityDefaults, $defaults);
        $city->setDefaults($defaults);
        if ($editCityCommand->thermographicPhotoImage instanceof File) {
            $city->setThermographicPhotoImageFile($editCityCommand->thermographicPhotoImage);
            $city->setThermographicPhotoImage($editCityCommand->thermographicPhotoImage);
        }
        $city->setStayUpToDate($editCityCommand->stayUpToDate);

        $this->cityRepository->add($city);
    }
}