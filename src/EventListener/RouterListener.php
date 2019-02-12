<?php

namespace One\CheckJeHuis\EventListener;

use One\CheckJeHuis\Service\FlowService;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;

class RouterListener
{
    /**
     * @var FlowService
     */
    private $flowService;

    public function __construct(FlowService $flowService)
    {
        $this->flowService = $flowService;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST) {
            return;
        }

        $request = $event->getRequest();
        $this->flowService->saveStep($request->get('_route'));
    }
}
