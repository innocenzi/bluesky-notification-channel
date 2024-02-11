<?php

namespace NotificationChannels\Bluesky\Facets;

final class Link extends Feature
{
    public function __construct(
        public readonly string $uri,
    ) {
    }

    public function getType(): string
    {
        return 'app.bsky.richtext.facet#link';
    }
}
