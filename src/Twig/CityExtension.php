<?php

namespace One\CheckJeHuis\Twig;

use One\CheckJeHuis\Entity\City;
use One\CheckJeHuis\Repository\CityRepository;
use Symfony\Component\HttpFoundation\Request;

class CityExtension extends \Twig_Extension
{
    /**
     * @var CityRepository
     */
    private $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function getFunctions()
    {
        return [
            new \Twig_Function('city', [$this, 'getCity']),
            new \Twig_Function('footerLogoImage', [$this, 'getFooterLogoImage']),
            new \Twig_Function('footerLogoLink', [$this, 'getFooterLogoLink']),
            new \Twig_Function('headerLogoImage', [$this, 'getHeaderLogoImage']),
            new \Twig_Function('backgroundColor', [$this, 'getBackgroundColor']),
            new \Twig_Function('backgroundColorHeader', [$this, 'getBackgroundColorHeader']),
            new \Twig_Function('textColor', [$this, 'getTextColor']),
            new \Twig_Function('textColorHeader', [$this, 'getTextColorHeader']),
            new \Twig_Function('buttonColor', [$this, 'getButtonColor']),
            new \Twig_Function('javascriptBottom', [$this, 'getJavascriptBottom']),
            new \Twig_Function('thermographicPhotoImage', [$this, 'getThermographicPhotoImage']),
        ];
    }

    public function getFooterLogoImage(Request $request)
    {
        $city = $this->getCity($request);
        if (!$city instanceof City) {
            return;
        }

        return $city->getFooterLogoImage();
    }

    public function getFooterLogoLink(Request $request)
    {
        $city = $this->getCity($request);
        if (!$city instanceof City) {
            return;
        }

        return $city->getFooterLogoLink();
    }

    public function getHeaderLogoImage(Request $request)
    {
        $city = $this->getCity($request);
        if (!$city instanceof City) {
            return;
        }

        return $city->getHeaderLogoImage();
    }

    public function getCity(Request $request)
    {
        if (!$request->getSession()) {
            return;
        }
        if (!$request->getSession()->has('city')) {
            return;
        }

        $cityId = $request->getSession()->get('city');
        return $this->cityRepository->find($cityId);
    }

    public function getBackgroundColor(Request $request)
    {
        $city = $this->getCity($request);
        if (!$city instanceof City) {
            return;
        }

        return $city->getBackgroundColor();
    }

    public function getBackgroundColorHeader(Request $request)
    {
        $city = $this->getCity($request);
        if (!$city instanceof City) {
            return;
        }

        return $city->getBackgroundColorHeader();
    }

    public function getTextColorHeader(Request $request)
    {
        $city = $this->getCity($request);
        if (!$city instanceof City) {
            return;
        }

        return $city->getTextColorHeader();
    }

    public function getTextColor(Request $request)
    {
        $city = $this->getCity($request);
        if (!$city instanceof City) {
            return;
        }

        return $city->getTextColor();
    }

    public function getButtonColor(Request $request)
    {
        $city = $this->getCity($request);
        if (!$city instanceof City) {
            return;
        }

        return $city->getButtonColor();
    }

    public function getJavascriptBottom(Request $request)
    {
        $city = $this->getCity($request);
        if (!$city instanceof City) {
            return;
        }

        return $city->getJavascriptBottom();
    }

    public function getThermographicPhotoImage(Request $request)
    {
        $city = $this->getCity($request);
        if (!$city instanceof City) {
            return;
        }

        return $city->getThermographicPhotoImage();
    }

    public function getName()
    {
        return 'city';
    }
}
