<?php

namespace One\CheckJeHuis\Command;

use One\CheckJeHuis\Entity\City;

class DuplicateCityCommand
{
    public $name;

    public $email;

    public $cityName;

    public $postalCode;

    public $prefillCity;

    public $nisNumber;

    public $url;

    public $headerLogoImage;

    public $footerLogoImage;

    public $footerLogoLink;

    public $backgroundColor = 'FFFFFF';

    public $backgroundColorHeader = '2c3e48';

    public $buttonColor = '20bec7';

    public $textColor = '000000';

    public $textColorHeader = 'FFFFFF';

    public $showInDropdown = true;

    public $showSpecificInfo = false;

    public $city;

    public $javascriptBottom;

    public $sunPotentialMapLink;

    public $thermographicPhotoLink;

    public $thermographicPhotoImage;

    public $stayUpToDate;

    public $defaultBuildingType;

    public $defaultBuildYear;

    private function __construct(City $city)
    {
        $this->city = $city;
    }

    public static function duplicateFromCity(City $city)
    {
        return new self($city);
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
}
