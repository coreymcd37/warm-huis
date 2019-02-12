<?php

namespace One\CheckJeHuis\Controller;

use One\CheckJeHuis\Service\FlowService;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FlowController extends AbstractController
{
    /**
     * @var FlowService
     */
    private $flowService;

    public function __construct(FlowService $flowService)
    {
        $this->flowService = $flowService;
    }

    public function nextAction($route)
    {
        $nextStep = $this->flowService->getNextStep($route);

        return new RedirectResponse($this->generateUrl($nextStep));
    }
}
