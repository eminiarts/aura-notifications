<?php

namespace Aura\Notifications\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Aura\Notifications\Notifications
 */
class Notifications extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Aura\Notifications\Notifications::class;
    }
}
