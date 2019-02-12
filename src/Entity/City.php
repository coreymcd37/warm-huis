<?php

namespace One\CheckJeHuis\Entity;

use Symfony\Component\HttpFoundation\File\File;

class City
{
    const STAY_UP_TO_DATE_HIDE = 0;
    const STAY_UP_TO_DATE_NOT_CHECKED = 1;
    const STAY_UP_TO_DATE_CHECKED = 2;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $cityName;

    /**
     * @var string
     */
    protected $postalCode;

    /**
     * @var bool
     */
    protected $prefillCity;

    /**
     * @var string
     */
    protected $nisNumber;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $headerLogoImage;

    /**
     * @var string
     */
    protected $headerLogoImageFile;

    /**
     * @var string
     */
    protected $footerLogoLink;

    /**
     * @var string
     */
    protected $footerLogoImage;

    /**
     * @var string
     */
    protected $footerLogoImageFile;

    /**
     * @var string
     */
    protected $backgroundColor;

    /**
     * @var string
     */
    protected $backgroundColorHeader;

    /**
     * @var string
     */
    protected $buttonColor;

    /**
     * @var string
     */
    protected $textColor;

    /**
     * @var string
     */
    protected $textColorHeader;

    /**
     * @var boolean
     */
    protected $showInDropdown;

    /**
     * @var boolean
     */
    protected $showSpecificInfo;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $javascriptBottom;

    /**
     * @var string
     */
    protected $sunPotentialMapLink;

    /**
     * @var string
     */
    protected $thermographicPhotoLink;

    /**
     * @var string
     */
    protected $thermographicPhotoImage;

    /**
     * @var string
     */
    protected $thermographicPhotoImageFile;

    /**
     * @var int
     */
    protected $stayUpToDate;


    protected $defaults = [];


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getNisNumber()
    {
        return $this->nisNumber;
    }

    /**
     * @param string $nisNumber
     */
    public function setNisNumber($nisNumber)
    {
        $this->nisNumber = $nisNumber;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getHeaderLogoImage()
    {
        return $this->headerLogoImage;
    }

    /**
     * @param string $headerLogoImage
     */
    public function setHeaderLogoImage($headerLogoImage)
    {
        $this->headerLogoImage = $headerLogoImage;
    }

    /**
     * @return string
     */
    public function getFooterLogoLink()
    {
        return $this->footerLogoLink;
    }

    /**
     * @param string $footerLogoLink
     */
    public function setFooterLogoLink($footerLogoLink)
    {
        $this->footerLogoLink = $footerLogoLink;
    }

    /**
     * @return string
     */
    public function getFooterLogoImage()
    {
        return $this->footerLogoImage;
    }

    /**
     * @param string $footerLogoImage
     */
    public function setFooterLogoImage($footerLogoImage)
    {
        $this->footerLogoImage = $footerLogoImage;
    }

    /**
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * @param string $backgroundColor
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
    }

    /**
     * @return string
     */
    public function getButtonColor()
    {
        return $this->buttonColor;
    }

    /**
     * @param string $buttonColor
     */
    public function setButtonColor($buttonColor)
    {
        $this->buttonColor = $buttonColor;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return bool
     */
    public function showInDropdown()
    {
        return $this->showInDropdown;
    }

    /**
     * @param bool $showInDropdown
     */
    public function setShowInDropdown($showInDropdown)
    {
        $this->showInDropdown = $showInDropdown;
    }

    /**
     * @return bool
     */
    public function showSpecificInfo()
    {
        return $this->showSpecificInfo;
    }

    /**
     * @param bool $showSpecificInfo
     */
    public function setShowSpecificInfo($showSpecificInfo)
    {
        $this->showSpecificInfo = $showSpecificInfo;
    }

    /**
     * @return string
     */
    public function getCityName()
    {
        return $this->cityName;
    }

    /**
     * @param string $cityName
     */
    public function setCityName($cityName)
    {
        $this->cityName = $cityName;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return bool
     */
    public function getPrefillCity()
    {
        return $this->prefillCity;
    }

    /**
     * @param bool $prefillCity
     */
    public function setPrefillCity($prefillCity)
    {
        $this->prefillCity = $prefillCity;
    }

    public function getHeaderLogoImageFile()
    {
        return $this->headerLogoImageFile;
    }

    public function setHeaderLogoImageFile(File $headerLogoImageFile)
    {
        $this->headerLogoImageFile = $headerLogoImageFile;

        if ($headerLogoImageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * @return string
     */
    public function getFooterLogoImageFile()
    {
        return $this->footerLogoImageFile;
    }

    public function setFooterLogoImageFile($footerLogoImageFile)
    {
        $this->footerLogoImageFile = $footerLogoImageFile;

        if ($footerLogoImageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getJavascriptBottom()
    {
        return $this->javascriptBottom;
    }

    /**
     * @param string $javascriptBottom
     */
    public function setJavascriptBottom($javascriptBottom)
    {
        $this->javascriptBottom = $javascriptBottom;
    }

    /**
     * @return string
     */
    public function getSunPotentialMapLink()
    {
        return $this->sunPotentialMapLink;
    }

    /**
     * @param string $sunPotentialMapLink
     */
    public function setSunPotentialMapLink($sunPotentialMapLink)
    {
        $this->sunPotentialMapLink = $sunPotentialMapLink;
    }

    /**
     * @return string
     */
    public function getThermographicPhotoLink()
    {
        return $this->thermographicPhotoLink;
    }

    /**
     * @param string $thermographicPhotoLink
     */
    public function setThermographicPhotoLink($thermographicPhotoLink)
    {
        $this->thermographicPhotoLink = $thermographicPhotoLink;
    }

    /**
     * @return string
     */
    public function getThermographicPhotoImage()
    {
        return $this->thermographicPhotoImage;
    }

    /**
     * @param string $thermographicPhotoImage
     */
    public function setThermographicPhotoImage($thermographicPhotoImage)
    {
        $this->thermographicPhotoImage = $thermographicPhotoImage;
    }

    /**
     * @return string
     */
    public function getThermographicPhotoImageFile()
    {
        return $this->thermographicPhotoImageFile;
    }

    /**
     * @param string $thermographicPhotoImageFile
     */
    public function setThermographicPhotoImageFile($thermographicPhotoImageFile)
    {
        $this->thermographicPhotoImageFile = $thermographicPhotoImageFile;
    }

    /**
     * @return int
     */
    public function getStayUpToDate()
    {
        return $this->stayUpToDate;
    }

    /**
     * @param int $stayUpToDate
     */
    public function setStayUpToDate($stayUpToDate)
    {
        $this->stayUpToDate = $stayUpToDate;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * @return string
     */
    public function getBackgroundColorHeader()
    {
        return $this->backgroundColorHeader;
    }

    /**
     * @param string $backgroundColorHeader
     */
    public function setBackgroundColorHeader($backgroundColorHeader)
    {
        $this->backgroundColorHeader = $backgroundColorHeader;
    }

    /**
     * @return string
     */
    public function getTextColor()
    {
        return $this->textColor;
    }

    /**
     * @param string $textColor
     */
    public function setTextColor($textColor)
    {
        $this->textColor = $textColor;
    }

    /**
     * @return string
     */
    public function getTextColorHeader()
    {
        return $this->textColorHeader;
    }

    /**
     * @param string $textColorHeader
     */
    public function setTextColorHeader($textColorHeader)
    {
        $this->textColorHeader = $textColorHeader;
    }
}
