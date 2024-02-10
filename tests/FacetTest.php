<?php

use NotificationChannels\Bluesky\BlueskyClient;
use NotificationChannels\Bluesky\RichText\Facets\Facet;
use NotificationChannels\Bluesky\Tests\Factories\BlueskyClientResponseFactory;
use Pest\Expectation;

it('detects links', function (string $url, array $positions) {
    $client = resolve(BlueskyClient::class);
    $facets = Facet::resolveFacets("Hello please take a look at {$url}, this is pretty cool website", $client);

    expect($facets)->sequence(
        fn (Expectation $facet) => $facet->toArray()->toBe([
            '$type' => 'app.bsky.richtext.facet',
            'index' => [
                'byteStart' => $positions[0],
                'byteEnd' => $positions[1],
            ],
            'features' => [
                [
                    '$type' => 'app.bsky.richtext.facet#link',
                    'uri' => $url,
                ],
            ],
        ]),
    );
})->with([
    ['https://innocenzi.dev', [28, 49]],
    ['https://innocenzi.dev/', [28, 50]],
    ['https://bsky.app', [28, 44]],
    ['https://www.bsky.app', [28, 48]],
    ['https://bsky.app?foo=bar', [28, 52]],
    ['https://bsky.app?foo=bar#baz', [28, 56]],
]);

it('detect multiple mentions', function () {
    BlueskyClientResponseFactory::fake([
        BlueskyClient::RESOLVE_HANDLE_ENDPOINT => [
            'did' => 'did:plc:123',
        ],
    ]);

    $client = resolve(BlueskyClient::class);
    $facets = Facet::resolveFacets('Hello @innocenzi.dev and @laravelnews.bsky.social', $client);

    expect($facets)->sequence(
        fn (Expectation $facet) => $facet->toArray()->toBe([
            '$type' => 'app.bsky.richtext.facet',
            'index' => [
                'byteStart' => 6,
                'byteEnd' => 20,
            ],
            'features' => [
                [
                    '$type' => 'app.bsky.richtext.facet#mention',
                    'did' => 'did:plc:123',
                ],
            ],
        ]),
        fn (Expectation $facet) => $facet->toArray()->toBe([
            '$type' => 'app.bsky.richtext.facet',
            'index' => [
                'byteStart' => 25,
                'byteEnd' => 49,
            ],
            'features' => [
                [
                    '$type' => 'app.bsky.richtext.facet#mention',
                    'did' => 'did:plc:123',
                ],
            ],
        ]),
    );
});

it('detects multiple facets', function () {
    BlueskyClientResponseFactory::fake();

    $client = resolve(BlueskyClient::class);
    $facets = Facet::resolveFacets('Hi @innocenzi.dev, is your website https://innocenzi.dev?', $client);

    expect($facets)->sequence(
        fn (Expectation $facet) => $facet->toArray()->toBe([
            '$type' => 'app.bsky.richtext.facet',
            'index' => [
                'byteStart' => 3,
                'byteEnd' => 17,
            ],
            'features' => [
                [
                    '$type' => 'app.bsky.richtext.facet#mention',
                    'did' => 'did:plc:sa57ykejomjswkuoktilt3sz',
                ],
            ],
        ]),
        fn (Expectation $facet) => $facet->toArray()->toBe([
            '$type' => 'app.bsky.richtext.facet',
            'index' => [
                'byteStart' => 35,
                'byteEnd' => 56,
            ],
            'features' => [
                [
                    '$type' => 'app.bsky.richtext.facet#link',
                    'uri' => 'https://innocenzi.dev',
                ],
            ],
        ]),
    );
});
