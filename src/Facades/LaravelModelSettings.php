<?php

namespace Mrdth\LaravelModelSettings\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mrdth\LaravelModelSettings\LaravelModelSettings
 */
class LaravelModelSettings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mrdth\LaravelModelSettings\LaravelModelSettings::class;
    }
}
