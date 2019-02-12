<?php

namespace One\CheckJeHuis\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use One\CheckJeHuis\Repository\BuildCostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BuildCostController extends Controller
{
    private $buildCostRepository;
    private $entityManager;

    public function __construct(BuildCostRepository $buildCostRepository, EntityManagerInterface $entityManager)
    {
        $this->buildCostRepository = $buildCostRepository;
        $this->entityManager = $entityManager;
    }

    public function indexAction()
    {
        return $this->render(':BuildCost:index.html.twig', array(
            'costs' => $this->buildCostRepository->getAll(),
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateBuildCostsAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $costs = $request->get('cost');

            foreach ($costs as $id => $val) {
                if (is_numeric($val)) {
                    $cost = $this->buildCostRepository->getCost($id);
                    $cost->setValue($val);
                    $this->entityManager->persist($cost);
                }
            }

            $this->entityManager->flush();
        }

        return $this->redirect($this->generateUrl('admin_buildcosts'));
    }
}
