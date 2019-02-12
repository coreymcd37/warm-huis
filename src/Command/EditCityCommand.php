<?php

namespace One\CheckJeHuis\Command;

use One\CheckJeHuis\Entity\City;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EditCityCommand
{
    public $id;

    public $name;

    public $cityName;

    public $postalCode;

    public $prefillCity;

    public $email;

    public $nisNumber;

    public $url;

    public $headerLogoImage;

    public $deleteHeaderLogoImage = false;

    public $footerLogoImage;

    public $deleteFooterLogoImage = false;

    public $footerLogoLink;

    public $backgroundColor;

    public $backgroundColorHeader;

    public $buttonColor;

    public $textColor;

    public $textColorHeader;

    public $showInDropdown;

    public $showSpecificInfo;

    public $javascriptBottom;

    public $sunPotentialMapLink;

    public $thermographicPhotoLink;

    public $thermographicPhotoImage;

    public $deleteThermographicPhotoImage = false;

    public $stayUpToDate;

    public $defaultBuildingType;

    public $defaultBuildYear;

    private function __construct(
        $id,
        $name,
        $email,
        $nisNumber,
        $cityName,
        $postalCode,
        $prefillCity,
        $url,
        $headerLogoImage,
        $footerLogoImage,
        $footerLogoLink,
        $backgroundColor,
        $backgroundColorHeader,
        $buttonColor,
        $textColor,
        $textColorHeader,
        $showInDropdown,
        $showSpecificInfo,
        $javascriptBottom,
        $sunPotentialMapLink,
        $thermographicPhotoLink,
        $thermographicPhotoImage,
        $stayUpToDate,
        $defaultBuildingType,
        $defaultBuildYear
    ){
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->nisNumber = $nisNumber;
        $this->cityName = $cityName;
        $this->postalCode = $postalCode;
        $this->prefillCity = $prefillCity;
        $this->url = $url;
        $this->headerLogoImage = new File($headerLogoImage, false);
        $this->footerLogoImage = new File($footerLogoImage, false);
        $this->footerLogoLink = $footerLogoLink;
        $this->backgroundColor = $backgroundColor;
        $this->backgroundColorHeader = $backgroundColorHeader;
        $this->buttonColor = $buttonColor;
        $this->textColor = $textColor;
        $this->textColorHeader = $textColorHeader;
        $this->showInDropdown = $showInDropdown;
        $this->showSpecificInfo = $showSpecificInfo;
        $this->javascriptBottom = $javascriptBottom;
        $this->sunPotentialMapLink = $sunPotentialMapLink;
        $this->thermographicPhotoLink = $thermographicPhotoLink;
        $this->thermographicPhotoImage = new File($thermographicPhotoImage, false);
        $this->stayUpToDate = $stayUpToDate;
        $this->defaultBuildingType = $defaultBuildingType;
        $this->defaultBuildYear = $defaultBuildYear;
    }

    public static function createFromCity(City $city)
    {
        $defaults = $city->getDefaults();
        $defaultBuildingType = $defaults['building_type'] ?? null;
        $defaultBuildYear = $defaults['build_year'] ?? null;

        return new self(
            $city->getId(),
            $city->getName(),
            $city->getEmail(),
            $city->getNisNumber(),
            $city->getCityName(),
            $city->getPostalCode(),
            $city->getPrefillCity(),
            $city->getUrl(),
            $city->getHeaderLogoImage(),
            $city->getFooterLogoImage(),
            $city->getFooterLogoLink(),
            $city->getBackgroundColor(),
            $city->getBackgroundColorHeader(),
            $city->getButtonColor(),
            $city->getTextColor(),
            $city->getTextColorHeader(),
            $city->showInDropdown(),
            $city->showSpecificInfo(),
            $city->getJavascriptBottom(),
            $city->getSunPotentialMapLink(),
            $city->getThermographicPhotoLink(),
            $city->getThermographicPhotoImage(),
            $city->getStayUpToDate(),
            $defaultBuildingType,
            $defaultBuildYear
        );
    }

    /**
     * @return mixed
     */
    public function getHeaderLogoImage()
    {
        return $this->headerLogoImage;
    }

    /**
     * @param mixed $headerLogoImage
     */
    public function setHeaderLogoImage($headerLogoImage)
    {
        $this->headerLogoImage = $headerLogoImage;
    }

    /**
     * @return mixed
     */
    public function getFooterLogoImage()
    {
        return $this->footerLogoImage;
    }

    /**
     * @param mixed $footerLogoImage
     */
    public function setFooterLogoImage($footerLogoImage)
    {
        $this->footerLogoImage = $footerLogoImage;
    }

    public function getHeaderLogoImagePath()
    {
        if (!$this->headerLogoImage) {
            return;
        }

        return $this->headerLogoImage->getFilename();
    }

    public function getFooterLogoImagePath()
    {
        if (!$this->footerLogoImage) {
            return;
        }

        return $this->footerLogoImage->getFilename();
    }

    public function getThermographicPhotoImagePath()
    {
        if (!$this->thermographicPhotoImage) {
            return;
        }

        return $this->thermographicPhotoImage->getFilename();
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->textColor === $this->backgroundColor) {
            $context->buildViolation('Textcolor should be different from background color')
                ->atPath('textColor')
                ->addViolation();
        }
    }
}
