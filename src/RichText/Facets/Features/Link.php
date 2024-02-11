<?php

namespace NotificationChannels\Bluesky\RichText\Facets\Features;

use NotificationChannels\Bluesky\RichText\Facets\FacetFeature;

final class Link extends FacetFeature
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
