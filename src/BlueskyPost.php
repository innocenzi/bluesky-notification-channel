<?php

namespace NotificationChannels\Bluesky;

use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use NotificationChannels\Bluesky\Embeds\Embed;
use NotificationChannels\Bluesky\Facets\Facet;

class BlueskyPost
{
    use Conditionable;
    use Macroable;
    use Tappable;

    private bool $automaticallyResolvesEmbeds = true;
    private bool $automaticallyResolvesFacets = true;

    private function __construct(
        public string $text = '',
        public array $facets = [],
        public ?Embed $embed = null,
        public array $languages = [],
        public ?string $embedUrl = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'text' => $this->text,
            'facets' => array_map(
                callback: fn (array|Facet $facet) => \is_array($facet) ? $facet : $facet->toArray(),
                array: $this->facets,
            ),
            'embed' => $this->embed?->toArray(),
            'langs' => $this->languages,
        ]);
    }

    public static function make(): static
    {
        return new static();
    }

    /**
     * Sets the embed for this post.
     * Note that by default, supported embeds are resolved automatically.
     */
    public function embed(?Embed $embed = null): static
    {
        $this->embed = $embed;

        return $this;
    }

    /**
     * Disables automatic embed resolution.
     */
    public function withoutAutomaticEmbeds(): static
    {
        $this->automaticallyResolvesEmbeds = false;

        return $this;
    }

    /**
     * Whether automatic embed resolution is enabled.
     */
    public function automaticallyResolvesEmbeds(): bool
    {
        return $this->automaticallyResolvesEmbeds;
    }

    /**
     * Disables automatic facets resolution.
     */
    public function withoutAutomaticFacets(): static
    {
        $this->automaticallyResolvesFacets = false;

        return $this;
    }

    /**
     * Whether automatic facet resolution is enabled.
     */
    public function automaticallyResolvesFacets(): bool
    {
        return $this->automaticallyResolvesFacets;
    }

    /**
     * Sets the language(s) of the post.
     *
     * @see https://www.docs.bsky.app/blog/create-post#setting-the-posts-language
     */
    public function language(string|array $language): static
    {
        $this->languages = Arr::wrap($language);

        return $this;
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

    /**
     * Sets the URL to be resolved as an embed.
     */
    public function embedUrl(string $embedUrl): static
    {
        $this->embedUrl = $embedUrl;

        return $this;
    }
}
