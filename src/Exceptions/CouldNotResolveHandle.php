<?php

namespace NotificationChannels\Bluesky\Exceptions;

final class CouldNotResolveHandle extends BlueskyException
{
    protected static function getDefaultMessage(): string
    {
        return 'Could not resolve handle';
    }
}
