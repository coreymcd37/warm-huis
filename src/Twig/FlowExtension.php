<?php

namespace One\CheckJeHuis\Twig;


use One\CheckJeHuis\Service\FlowService;

class FlowExtension extends \Twig_Extension
{
    /**
     * @var FlowService
     */
    private $flowService;

    public function __construct(FlowService $flowService)
    {
        $this->flowService = $flowService;
    }

    public function getFunctions()
    {
        return [
            new \Twig_Function('canGoToRoute', [$this, 'canGoToRoute']),
        ];
    }

    public function canGoToRoute($route)
    {
        return $this->flowService->canGoToRoute($route);
    }
}