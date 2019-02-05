<?php

namespace ssemco\mobilemoney\Facades;

use Illuminate\Support\Facades\Facade;

class MobileMoney extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mobilemoney';
    }
}
