<?php

namespace NotificationChannels\Bluesky\Exceptions;

final class CouldNotCreatePost extends BlueskyException
{
    protected static function getDefaultMessage(): string
    {
        return 'Could not create post';
    }
}
