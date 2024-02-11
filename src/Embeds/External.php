<?php

namespace NotificationChannels\Bluesky\Embeds;

final class External extends Embed
{
    public function __construct(
        public readonly string $uri,
        public readonly string $title,
        public readonly string $description,
        public readonly array $thumb,
    ) {
    }

    public function getType(): string
    {
        return 'app.bsky.embed.external';
    }

    public function toArray(): array
    {
        return [
            '$type' => $this->getType(),
            'external' => $this->serializeProperties(),
        ];
    }
}
