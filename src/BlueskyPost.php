<?php

namespace NotificationChannels\Bluesky;

use NotificationChannels\Bluesky\RichText\Facets\Facet;

class BlueskyPost
{
    private function __construct(
        public string $text = '',
        public array $facets = [],
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

    /**
     * Adds a facet to the post. Note that most facets are resolved automatically.
     */
    public function facet(Facet $facet): static
    {
        $this->facets[] = $facet;

        return $this;
    }

    /**
     * Adds multiple facets to the post. Note that most facets are resolved automatically.
     *
     * @param Facet[] $facet
     */
    public function facets(array $facets): static
    {
        $this->facets = array_merge($this->facets, $facets);

        return $this;
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
