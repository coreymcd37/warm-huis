<?php

namespace One\CheckJeHuis\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Adds a P3P header to allow the application to work in an iframe in internet explorer
 *
 * @package One\CheckJeHuis\EventListener
 */
class P3PResponseListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        // only execute for text/html files
        if ($request->getRequestFormat() !== 'html') {
            return;
        }

        // set the "P3P" header of the response
        $event->getResponse()->headers->set('P3P', 'CP="CAO PSA OUR"');
    }
}
