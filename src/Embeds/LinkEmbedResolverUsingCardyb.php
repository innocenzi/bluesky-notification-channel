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

        $embed = Http::get(self::ENDPOINT, [
            'url' => $firstLink->getFeatures()[0]->uri,
        ]);

        if ($embed->json('error')) {
            return null;
        }

        return new External(
            uri: $embed->json('url'),
            title: $embed->json('title'),
            description: $embed->json('description'),
            thumb: $bluesky
                ->uploadBlob($embed->json('image'))
                ->toArray(),
        );
    }
}
