<?php

namespace NotificationChannels\Bluesky\Exceptions;

abstract class BlueskyClientException extends BlueskyException
{
    public static function create(int $status, ?string $error, ?string $message): static
    {
        $suffix = static::createSuffix($status, $error, $message);

        return match ($error) {
            'AccountTakedown' => new static("Account is suspended ({$suffix})"),
            'ExpiredToken' => new static("Token is expired ({$suffix})"),
            'InvalidToken' => new static("Token is invalid ({$suffix})"),
            default => new static(static::getDefaultMessage() . " ({$suffix})")
        };
    }

    abstract protected static function getDefaultMessage(): string;

    protected static function createSuffix(int $status, ?string $error, ?string $message): string
    {
        return str($status)
            ->when($error || $message)
            ->append(', ' . implode(': ', array_filter([$error, $message])))
            ->toString();
    }
}
