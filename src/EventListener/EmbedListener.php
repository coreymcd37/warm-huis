<?php

namespace One\CheckJeHuis\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class EmbedListener
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        $session = $request->getSession();
        if (!$session) {
            return;
        }

        if ($request->query->has('embed')) {
            $session->set('embed', $request->query->get('embed', false));
        }
    }
}
