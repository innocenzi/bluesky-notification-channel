<?php

namespace NotificationChannels\Bluesky\Exceptions;

final class CouldNotCreateSession extends BlueskyClientException
{
    protected static function getDefaultMessage(): string
    {
        return 'Could not create session';
    }
}
