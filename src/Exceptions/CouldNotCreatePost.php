<?php

namespace NotificationChannels\Bluesky\Exceptions;

final class CouldNotCreatePost extends BlueskyClientException
{
    protected static function getDefaultMessage(): string
    {
        return 'Could not create post';
    }
}
