<?php

namespace NotificationChannels\Bluesky;

use NotificationChannels\Bluesky\RichText\Facets\Facet;

class BlueskyPost
{
    private function __construct(
        public string $text = '',
        public readonly array $facets = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'facets' => array_map(
                callback: fn (array|Facet $facet) => \is_array($facet) ? $facet : $facet->toArray(),
                array: $this->facets,
            ),
        ];
    }

    public static function make(): static
    {
        return new static();
    }

    public function resolveFacets(BlueskyClient $client): static
    {
        return new static(
            text: $this->text,
            facets: Facet::resolveFacets($this->text, $client),
        );
    }

    /**
     * Sets the post's text.
     */
    public function text(?string $text): static
    {
        $this->text = $text ?? '';

        return $this;
    }
}
