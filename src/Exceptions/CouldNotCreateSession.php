<?php

namespace NotificationChannels\Bluesky\Exceptions;

final class CouldNotCreateSession extends BlueskyException
{
    protected static function getDefaultMessage(): string
    {
        return 'Could not create session';
    }
}
