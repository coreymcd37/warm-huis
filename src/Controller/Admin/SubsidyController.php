<?php

namespace One\CheckJeHuis\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SubsidyController extends Controller
{
    public function indexAction()
    {
        $service = $this->get('one.check_je_huis.service.subsidy');

        return $this->render(':Subsidy:index.html.twig', array(
            'subsidyCategories' => $service->getAllSubsidyCategories(),
        ));
    }

    /**
     * @Security("has_role('ROLE_CITY')")
     */
    public function updateAction(Request $request)
    {
        $service = $this->get('one.check_je_huis.service.subsidy');

        if ($request->getMethod() == 'POST') {

            // update category labels
            foreach ($request->get('subsidy-cat-label') as $id => $label) {
                $cat = $service->getSubsidyCategory($id);
//                $cat->setLabel($label);
                $service->persist($cat, false);
            }

            // save subsidy configs

            $values = $request->get('subsidy-value');
            $maximums = $request->get('subsidy-max');
            $multipliers = $request->get('subsidy-multiplier');

            foreach ($values as $id => $val) {
                $max = $maximums[$id];
                $max = $max ?: 0;

                $multiplier = $multipliers[$id];

                if (is_numeric($val) && (is_numeric($max) || is_null($max))) {
                    $subsidy = $service->getSubsidy($id);
                    $subsidy->setValue($val);
                    $subsidy->setMax($max);
                    $subsidy->setMultiplier($multiplier);
                    $service->persist($subsidy, false);
                }
            }

            $service->persist(true);
        }

        return $this->redirect($this->generateUrl('admin_subsidies'));
    }
}
