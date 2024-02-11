<?php

namespace NotificationChannels\Bluesky\Facets;

final class Mention extends Feature
{
    public function __construct(
        public readonly string $did,
    ) {
    }

    public function getType(): string
    {
        return 'app.bsky.richtext.facet#mention';
    }
}
