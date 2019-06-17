<?php

namespace One\CheckJeHuis\Controller;

use One\CheckJeHuis\Entity\City;
use One\CheckJeHuis\Entity\Content;
use One\CheckJeHuis\Exception\CityNotFoundException;
use One\CheckJeHuis\Form\MailPlanType;
use One\CheckJeHuis\Repository\CityRepository;
use One\CheckJeHuis\Repository\ContentRepository;
use Symfony\Component\HttpFoundation\Request;

class PageController extends AbstractController
{
    private $contentRepository;
    /**
     * @var CityRepository
     */
    private $cityRepository;

    public function __construct(ContentRepository $contentRepository, CityRepository $cityRepository)
    {
        $this->contentRepository = $contentRepository;
        $this->cityRepository = $cityRepository;
    }

    public function indexAction(Request $request, $cityParameter = null)
    {
        $session = $request->getSession();
        $embed = false;
        if ($request->query->has('embed')) {
            $embed = $request->query->get('embed', false);
        }
        $session->set('embed', $embed);
        if ($cityParameter) {
            $city = $this->cityRepository->findOneBy(['url' => $cityParameter]);
        } else if ($session->has('city')) {
            $cityId = $session->get('city');
            $city = $this->cityRepository->find($cityId);
        } else {
            /** @var City $city */
            $city = $this->cityRepository->determineCity($request);
        }

        if (!$city instanceof City) {
            return $this->redirect($this->generateUrl('app_start'));
        }

        $session->set('city', $city->getId());
        $content = $this->contentRepository->getContentBySlug(Content::INTRO, $city);
        $popup = null;
        if ($city->showSpecificInfo()) {
            $popup = $this->contentRepository->getContentBySlug(Content::POPUP_START, $city);
        }

        return $this->render(':Page:index.html.twig', array(
            'content' => $content,
            'popup' => $popup,
        ));
    }

    public function moreInfoAction()
    {
        $content = $this->contentRepository->getContentBySlug(Content::INFO);

        return $this->render(':Page:more-info.html.twig', array(
            'content' => $content,
        ));
    }

    public function planAction(Request $request, $download = false)
    {
        $house = $this->getSessionHouse();

        if (!$house) {
            return $this->noHouseRedirect();
        }

        $form = $this->createForm(MailPlanType::class, $house);
        $city = $house->getCity();

        return $this->render(':Page:plan.html.twig', array(
            'form' => $form->createView(),
            'download' => $download,
            'hasAddressCookie' => $request->cookies->has(self::COOKIE_USER_ADDRESS),
            'pdf_popup_content' => $this->contentRepository->getContentBySlug(Content::PDF_POPUP, $city),
            'city' => $city,
            'plan_extra_content' => $this->contentRepository->getContentBySlug(Content::FOUR_PLAN_EXTRA, $city),
        ));
    }

    public function startAction(Request $request)
    {
        $cities = $this->cityRepository->findAllForSelection(true);
        if ($request->getMethod() === 'POST') {
            $session = $request->getSession();
            $cityId = $request->request->get('city');
            $city = $this->cityRepository->find($cityId);
            if (!$city instanceof City) {
                throw new CityNotFoundException('Invalid city');
            }

            $session->set('city', $cityId);
            $house = $this->getSessionHouse();
            if ($house) {
                $house->setCity($city);
                $this->get('doctrine.orm.entity_manager')->persist($house);
                $this->get('doctrine.orm.entity_manager')->flush();
            }
            
            return $this->redirect($this->generateUrl('app_index'));
        }

        return $this->render(':Page:start.html.twig', [
            'cities' => $cities,
            'content' => $this->contentRepository->getContentBySlug(Content::START),
        ]);
    }

    public function privacyAction(Request $request)
    {
        $session = $request->getSession();
        $cityId = $session->get('city');

        return $this->render(':Page:privacy.html.twig', array(
            'privacy_content' => $this->contentRepository->getContentBySlug(Content::PRIVACY, $cityId),
        ));
    }
}
