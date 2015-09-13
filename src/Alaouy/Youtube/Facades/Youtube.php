<?php

namespace Alaouy\Youtube\Facades;

use Illuminate\Support\Facades\Facade;

class Youtube extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'youtube'; }
}