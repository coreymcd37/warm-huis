<?php

namespace One\CheckJeHuis\EventListener\Event;

use Symfony\Component\EventDispatcher\Event;

class HousesExportEvent extends Event
{
    private $filter;

    public function __construct($filter)
    {
        $this->filter = $filter;
    }

    public function getFilter()
    {
        return $this->filter;
    }

}
