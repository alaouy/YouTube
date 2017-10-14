<?php

namespace Alaouy\YouTube\Facades;

use Illuminate\Support\Facades\Facade;

class YouTube extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'youtube'; }
}
