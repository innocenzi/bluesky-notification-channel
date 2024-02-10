<?php

namespace NotificationChannels\Bluesky\Exceptions;

final class NoBlueskyChannel extends BlueskyException
{
    public static function create(string $class): self
    {
        return new self("The `toBluesky` method must be defined on [{$class}].");
    }
}
