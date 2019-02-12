<?php

namespace One\CheckJeHuis\Controller\Admin;

use League\Tactician\CommandBus;
use One\CheckJeHuis\Command\DuplicateCityCommand;
use One\CheckJeHuis\Command\EditCityCommand;
use One\CheckJeHuis\Entity\City;
use One\CheckJeHuis\Exception\DeleteOwnCityException;
use One\CheckJeHuis\Form\CreateCityType;
use One\CheckJeHuis\Form\EditCityType;
use One\CheckJeHuis\Repository\CityRepository;
use One\CheckJeHuis\Security\CityVoter;
use One\CheckJeHuis\Service\CityRemovalService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route(service="one.check_je_huis.controller.admin.city_controller")
 */
class CityController extends Controller
{
    /**
     * @var CityRepository
     */
    private $cityRepository;

    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var CityRemovalService
     */
    private $cityRemovalService;


    public function __construct(CityRepository $cityRepository, CommandBus $commandBus, CityRemovalService $cityRemovalService)
    {
        $this->cityRepository = $cityRepository;
        $this->commandBus = $commandBus;
        $this->cityRemovalService = $cityRemovalService;
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction()
    {
        $cities = $this->cityRepository->findAllForSelection(false);

        return $this->render(':Admin/City:list.html.twig', [
            'cities' => $cities,
        ]);
    }

    public function duplicateAction(Request $request, City $city)
    {
        $this->denyAccessUnlessGranted(CityVoter::DUPLICATE, $city);
        $duplicateCityCommand = DuplicateCityCommand::duplicateFromCity($city);
        $form = $this->createForm(CreateCityType::class, $duplicateCityCommand);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->commandBus->handle($duplicateCityCommand);
            $request->getSession()->getFlashBag()->add('info', 'Omgeving aangemaakt');

            return new RedirectResponse($this->generateUrl('admin_city_list'));
        }

        return $this->render(':Admin/City:duplicate.html.twig', [
            'form' => $form->createView(),
            'city' => $city,
        ]);
    }

    public function editAction(Request $request, City $city)
    {
        $this->denyAccessUnlessGranted(CityVoter::EDIT, $city);
        $cityCommand = EditCityCommand::createFromCity($city);
        $form = $this->createForm(EditCityType::class, $cityCommand);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->commandBus->handle($cityCommand);
            $request->getSession()->getFlashBag()->add('info', 'Omgeving aangepast');

            return new RedirectResponse($this->generateUrl('admin_city_list'));
        }

        return $this->render(':Admin/City:edit.html.twig', [
            'form' => $form->createView(),
            'city' => $city,
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, City $city)
    {
        $this->denyAccessUnlessGranted(CityVoter::DELETE, $city);

        $response = array(
            'html' => '',
        );
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_city_delete', ['city' => $city->getId()]))
            ->setMethod('DELETE')
            ->getForm();
        if ($request->getMethod() === 'DELETE') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->cityRemovalService->remove($city, $this->getUser());
                } catch (DeleteOwnCityException $exception) {
                    $request->getSession()->getFlashBag()->add('danger', 'Kan eigen omgeving niet verwijderen');

                    return new RedirectResponse($this->generateUrl('admin_city_list'));
                }
            }
            // In case you want to redirect.
            $request->getSession()->getFlashBag()->add('info', 'Omgeving verwijderd');

            return new RedirectResponse($this->generateUrl('admin_city_list'));
        }
        $render = $this->render(':Admin/City:delete_confirm.html.twig', array(
            'delete_form' => $form->createView(),
            'city' => $city,
        ));

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }
}