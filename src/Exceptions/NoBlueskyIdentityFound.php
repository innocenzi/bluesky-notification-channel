<?php

namespace NotificationChannels\Bluesky\Exceptions;

final class NoBlueskyIdentityFound extends \Exception
{
    public static function create(): self
    {
        return new self('No Bluesky identity could be found.');
    }
}
