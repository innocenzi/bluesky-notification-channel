<?php

namespace NotificationChannels\Bluesky\Exceptions;

final class CouldNotRefreshSession extends BlueskyException
{
    protected static function getDefaultMessage(): string
    {
        return 'Could not refresh session';
    }
}
