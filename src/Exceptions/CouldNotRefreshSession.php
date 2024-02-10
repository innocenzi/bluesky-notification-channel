<?php

namespace NotificationChannels\Bluesky\Exceptions;

final class CouldNotRefreshSession extends BlueskyClientException
{
    protected static function getDefaultMessage(): string
    {
        return 'Could not refresh session';
    }
}
