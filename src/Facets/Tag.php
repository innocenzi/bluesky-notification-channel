<?php

namespace NotificationChannels\Bluesky\Facets;

final class Tag extends Feature
{
    public function __construct(
        public readonly string $tag,
    ) {
    }

    public function getType(): string
    {
        return 'app.bsky.richtext.facet#tag';
    }
}
