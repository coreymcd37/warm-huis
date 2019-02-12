<?php

namespace One\CheckJeHuis\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use One\CheckJeHuis\Repository\RenewableRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RenewableController extends Controller
{
    private $renewableRepository;
    private $entityManager;

    public function __construct(RenewableRepository $renewableRepository, EntityManagerInterface $entityManager)
    {
        $this->renewableRepository = $renewableRepository;
        $this->entityManager = $entityManager;
    }

    public function indexAction()
    {
        return $this->render(
            ':Renewable:index.html.twig',
            array(
                'renewables' => $this->renewableRepository->getAll(),
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateRenewablesAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $renewables = $request->get('renewable');

            foreach ($renewables as $id => $val) {
                if (is_numeric($val)) {
                    $renewable = $this->renewableRepository->getRenewable($id);
                    $renewable->setValue($val);
                    $this->renewableRepository->add($renewable);
                }
            }

            $this->entityManager->flush();
        }

        return $this->redirect($this->generateUrl('admin_renewables'));
    }
}
