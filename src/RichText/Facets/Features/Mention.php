<?php

namespace NotificationChannels\Bluesky\RichText\Facets\Features;

use NotificationChannels\Bluesky\RichText\Facets\FacetFeature;

final class Mention extends FacetFeature
{
    public function __construct(
        public readonly string $did,
    ) {
    }

    protected function getType(): string
    {
        return 'app.bsky.richtext.facet#mention';
    }
}
