<?php

namespace NotificationChannels\Bluesky\RichText\Facets;

use NotificationChannels\Bluesky\BlueskyClient;
use NotificationChannels\Bluesky\Exceptions\BlueskyClientException;
use NotificationChannels\Bluesky\RichText\Facets\Features\Link;
use NotificationChannels\Bluesky\RichText\Facets\Features\Mention;

final class Facet
{
    public function __construct(
        private readonly array $range,
        private readonly array $features,
    ) {
        // TODO: validation and exceptions
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
            'features' => array_map(fn (FacetFeature $feature) => $feature->toArray(), $this->features),
        ];
    }

    /** @return array<Facet> */
    public static function resolveFacets(string $text, BlueskyClient $client): array
    {
        // https://www.docs.bsky.app/docs/advanced-guides/post-richtext#text-encoding-and-indexing
        $text = mb_convert_encoding($text, 'utf-8');

        return [
            ...self::detectMentions($text, $client),
            ...self::detectLinks($text),
        ];
    }

    /** @return array<Facet> */
    private static function detectMentions(string $text, BlueskyClient $client): array
    {
        $mentionRegexp = '/(^|\\s|\\()(@)([a-zA-Z0-9.-]+)(\\b)/';

        preg_match_all($mentionRegexp, $text, $matches, \PREG_OFFSET_CAPTURE);

        return array_filter(array_map(function (array $match) use ($client) {
            [$handle, $position] = $match;

            try {
                $did = $client->resolveHandle($handle);
            } catch (BlueskyClientException) {
                return null;
            }

            return new Facet(
                range: [
                    $position - 1, // include @
                    $position + mb_strlen($handle),
                ],
                features: [
                    new Mention(did: $did),
                ],
            );
        }, $matches[3]));
    }

    private static function detectLinks(string $text): array
    {
        $urlRegExp = '/[$|\\W](https?:\\/\\/(www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}\\.[a-zA-Z0-9()]{1,6}\\b([-a-zA-Z0-9()@:%_\\+.~#?&\\/\\/=]*[-a-zA-Z0-9@%_\\+~#\\/\\/=])?)/';

        preg_match_all($urlRegExp, $text, $matches, \PREG_OFFSET_CAPTURE);

        return array_map(function (array $match) {
            [$uri, $position] = $match;

            return new Facet(
                range: [
                    $position,
                    $position + mb_strlen($uri),
                ],
                features: [
                    new Link(uri: $uri),
                ],
            );
        }, $matches[1]);
    }
}
