<?php

namespace One\CheckJeHuis\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use One\CheckJeHuis\Entity\House;
use One\CheckJeHuis\Form\DefaultEnergyType;
use One\CheckJeHuis\Repository\DefaultEnergyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultEnergyController extends Controller
{
    private $defaultEnergyRepository;
    private $entityManager;

    public function __construct(DefaultEnergyRepository $defaultEnergyRepository, EntityManagerInterface $entityManager)
    {
        $this->defaultEnergyRepository = $defaultEnergyRepository;
        $this->entityManager = $entityManager;
    }

    public function indexAction(Request $request)
    {
        $filter = $request->get('table-filter', array());

        return $this->render(':DefaultEnergy:index.html.twig', array(
            'buildingTypes'     => House::getBuildingTypes(),
            'buildingSizes'     => House::getSizes(),
            'years'             => House::getYears(),
            'defaults'          => $this->defaultEnergyRepository->getAllEnergy($filter),
            'filter'            => $filter,
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateDefaultEnergyAction($id, Request $request)
    {
        $energy = $this->defaultEnergyRepository->getEnergyById($id);

        $form = $this->createForm(DefaultEnergyType::class, $energy);
        $success = true;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->entityManager->persist($energy);
                $this->entityManager->flush();
            } else {
                $success = false;
            }
        }

        $formTemplate = $this->renderView(':DefaultEnergy:update-default-energy.html.twig', array(
            'form'          => $form->createView(),
            'energy'        => $energy,
        ));

        // return a JSON response with the rendered form HTML as a property
        return new JsonResponse(array(
            'success'   => $success,
            'template'  => $formTemplate
        ));
    }
}
