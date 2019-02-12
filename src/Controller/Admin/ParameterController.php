<?php

namespace One\CheckJeHuis\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use One\CheckJeHuis\Service\ParameterService;

class ParameterController extends Controller
{
    public function indexAction()
    {
        /** @var ParameterService $service */
        $service = $this->get('one.check_je_huis.service.parameter');

        return $this->render(':Parameter:index.html.twig', array(
            'parameters' => $service->getAll(),
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateParametersAction(Request $request)
    {
        $paramService = $this->get('one.check_je_huis.service.parameter');

        if ($request->getMethod() == 'POST') {
            $parameters = $request->get('parameter');

            foreach ($parameters as $id => $val) {
                if (is_numeric($val)) {
                    $parameter = $paramService->getParameter($id);
                    $parameter->setValue($val);
                    $paramService->persist($parameter, false);
                }
            }

            $paramService->persist(true);
        }

        return $this->redirect($this->generateUrl('admin_parameters'));
    }
}
