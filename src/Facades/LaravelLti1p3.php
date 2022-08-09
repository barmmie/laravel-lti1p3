<?php

namespace Wien\LaravelLti1p3\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Wien\LaravelLti1p3\LaravelLti1p3
 */
class LaravelLti1p3 extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Wien\LaravelLti1p3\LaravelLti1p3::class;
    }
}
