<?php

namespace NotificationChannels\Bluesky\Exceptions;

final class CouldNotUploadBlob extends BlueskyClientException
{
    public static function couldNotLoadImage(): self
    {
        return new self('The specified image could not be loaded.');
    }

    protected static function getDefaultMessage(): string
    {
        return 'Could not upload blob';
    }
}
