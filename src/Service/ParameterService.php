<?php

namespace One\CheckJeHuis\Service;

use One\CheckJeHuis\Calculator\Parameters;
use One\CheckJeHuis\Entity\Parameter;

class ParameterService extends AbstractService
{
    /**
     * @return Parameter[]
     */
    public function getAll()
    {
        $repo = $this->getDoctrine()->getRepository('Entity:Parameter');

        return $repo->findAll();
    }

    /**
     * @param int $id
     * @return Parameter
     */
    public function getParameter($id)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('Entity:Parameter');

        return $repo->find($id);
    }

    /**
     * @param string $slug
     * @return Parameter
     */
    public function getParameterBySlug($slug)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('Entity:Parameter');

        return $repo->findOneBy(array('slug' => $slug));
    }

    /**
     * @return Parameters
     */
    public function getCalculationParameters()
    {
        $params = new Parameters();

        $params->setPriceGas(
            $this->getParameterBySlug(Parameter::PARAM_PRICE_GAS)->getValue()
        );
        $params->setPriceElec(
            $this->getParameterBySlug(Parameter::PARAM_PRICE_ELEC)->getValue()
        );
        $params->setPriceOil(
            $this->getParameterBySlug(Parameter::PARAM_PRICE_OIL)->getValue()
        );
        $params->setCo2PerKwh(
            $this->getParameterBySlug(Parameter::PARAM_CO2_KWH)->getValue()
        );
        $params->setEnergyLoanThreshold(
            $this->getParameterBySlug(Parameter::PARAM_ENERGY_LOAN_THRESHOLD)->getValue()
        );
        $params->setEnergyLoanInterestRate(
            $this->getParameterBySlug(Parameter::PARAM_ENERGY_LOAN_INTEREST_RATE)->getValue()
        );
        $params->setEnergyLoanDuration(
            $this->getParameterBySlug(Parameter::PARAM_ENERGY_LOAN_DURATION)->getValue()
        );
        $params->setMortgageInterestRate(
            $this->getParameterBySlug(Parameter::PARAM_MORTGAGE_INTEREST_RATE)->getValue()
        );
        $params->setMortgageDuration(
            $this->getParameterBySlug(Parameter::PARAM_MORTGAGE_DURATION)->getValue()
        );

        return $params;
    }
}
