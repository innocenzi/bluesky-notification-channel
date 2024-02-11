<?php

namespace NotificationChannels\Bluesky\Facets;

final class Facet
{
    public function __construct(
        private readonly array $range,
        private readonly array $features,
    ) {
    }

    /**
     * Gets the features for this facet.
     */
    public function getFeatures(): array
    {
        return $this->features;
    }

    public function toArray(): array
    {
        return [
            '$type' => 'app.bsky.richtext.facet',
            'index' => [
                'byteStart' => $this->range[0],
                'byteEnd' => $this->range[1],
            ],
            'features' => array_map(fn (Feature $feature) => $feature->toArray(), $this->features),
        ];
    }
}
