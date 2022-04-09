<?php
/*
 * Copyright (c) - WDigital - 2022. 
 * @link https://wdigital.ch
 * @developer Florian Würtenberger <florian@wdigital.ch>
 */

namespace WDigital\KlickTippForLaravel;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Wdigital\KlicktippForLaravel\Skeleton\SkeletonClass
 */
class KlickTippForLaravelFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'klicktipp-for-laravel';
    }
}
