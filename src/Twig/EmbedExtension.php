<?php

namespace One\CheckJeHuis\Twig;

use Symfony\Component\HttpFoundation\Request;

class EmbedExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_Function('embed', [$this, 'embed']),
        ];
    }

    public function embed(Request $request)
    {
        return $request->getSession()->get('embed', false);
    }

    public function getName()
    {
        return 'embed';
    }
}
