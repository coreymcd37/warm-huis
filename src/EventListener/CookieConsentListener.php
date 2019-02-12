<?php

namespace One\CheckJeHuis\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CookieConsentListener
{
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

        if ($request->query->has('cookie-consent')) {
            $session->set('cookie-consent', $request->query->get('cookie-consent'));
        }
    }
}
