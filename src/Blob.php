<?php

namespace NotificationChannels\Bluesky;

final class Blob
{
    public function __construct(
        public readonly array $blob,
    ) {
    }

    public function toArray(): array
    {
        return $this->blob;
    }
}
