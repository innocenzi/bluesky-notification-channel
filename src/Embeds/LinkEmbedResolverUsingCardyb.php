<?php

namespace NotificationChannels\Bluesky\Embeds;

use Illuminate\Support\Facades\Http;
use NotificationChannels\Bluesky\BlueskyPost;
use NotificationChannels\Bluesky\BlueskyService;
use NotificationChannels\Bluesky\Facets\Facet;
use NotificationChannels\Bluesky\Facets\Feature;

final class LinkEmbedResolverUsingCardyb implements EmbedResolver
{
    public const ENDPOINT = 'https://cardyb.bsky.app/v1/extract';

    public function resolve(BlueskyService $bluesky, BlueskyPost $post): ?Embed
    {
        if (\count($post->facets) === 0) {
            return null;
        }

        /** @var Facet */
        $firstLink = collect($post->facets)->first(
            callback: fn (Facet $facet) => collect($facet->getFeatures())->first(
                callback: fn (Feature $feature) => $feature->getType() === 'app.bsky.richtext.facet#link',
            ),
        );

        if (!$firstLink) {
            return null;
        }

        return $this->createEmbedFromUrl($bluesky, $firstLink->getFeatures()[0]->uri);
    }

    public function createEmbedFromUrl(BlueskyService $bluesky, string $url): ?Embed
    {
        $embed = Http::get(self::ENDPOINT, [
            'url' => $url,
        ]);

        if ($embed->json('error')) {
            return null;
        }

        $thumbnail = $bluesky->uploadBlob($embed->json('image'));

        return new External(
            uri: $embed->json('url'),
            title: $embed->json('title'),
            description: $embed->json('description'),
            thumb: !\is_null($thumbnail) ? $thumbnail->toArray() : null,
        );
    }
}
