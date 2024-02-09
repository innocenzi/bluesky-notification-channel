<?php

namespace Innocenzi\BlueskyNotificationChannel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Innocenzi\BlueskyNotificationChannel\BlueskyNotificationChannel
 */
class BlueskyNotificationChannel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Innocenzi\BlueskyNotificationChannel\BlueskyNotificationChannel::class;
    }
}
