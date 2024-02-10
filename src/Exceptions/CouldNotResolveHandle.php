<?php

namespace NotificationChannels\Bluesky\Exceptions;

final class CouldNotResolveHandle extends BlueskyClientException
{
    protected static function getDefaultMessage(): string
    {
        return 'Could not resolve handle';
    }
}
