<?php

namespace NNV\L5FlyThumb\Facades;

use Illuminate\Support\Facades\Facade;

class L5FlyThumb extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'l5flythumb';
    }
}
