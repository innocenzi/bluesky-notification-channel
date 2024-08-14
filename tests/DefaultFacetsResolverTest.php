<?php

use NotificationChannels\Bluesky\BlueskyClient;
use NotificationChannels\Bluesky\BlueskyPost;
use NotificationChannels\Bluesky\BlueskyService;
use NotificationChannels\Bluesky\Facets\FacetsResolver;
use NotificationChannels\Bluesky\Tests\Factories\HttpResponsesFactory;
use Pest\Expectation;

it('detects links', function (string $url, array $positions) {
    /** @var FacetsResolver */
    $resolver = resolve(FacetsResolver::class);
    $facets = $resolver->resolve(
        bluesky: resolve(BlueskyService::class),
        post: BlueskyPost::make()->text("Hello please take a look at {$url}, this is pretty cool website"),
    );

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
    HttpResponsesFactory::fake([
        BlueskyClient::RESOLVE_HANDLE_ENDPOINT => [
            'did' => 'did:plc:123',
        ],
    ]);

    /** @var FacetsResolver */
    $resolver = resolve(FacetsResolver::class);
    $facets = $resolver->resolve(
        bluesky: resolve(BlueskyService::class),
        post: BlueskyPost::make()->text('Hello @innocenzi.dev and @laravelnews.bsky.social'),
    );

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
    HttpResponsesFactory::fake();

    /** @var FacetsResolver */
    $resolver = resolve(FacetsResolver::class);
    $facets = $resolver->resolve(
        bluesky: resolve(BlueskyService::class),
        post: BlueskyPost::make()->text('Hi @innocenzi.dev, is your website https://innocenzi.dev?'),
    );

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

it('detects tags', function (string $text, array $positions, string $tag) {
    /** @var FacetsResolver */
    $resolver = resolve(FacetsResolver::class);
    $facets = $resolver->resolve(
        bluesky: resolve(BlueskyService::class),
        post: BlueskyPost::make()->text($text),
    );

    expect($facets)->sequence(
        fn (Expectation $facet) => $facet->toArray()->toBe([
            '$type' => 'app.bsky.richtext.facet',
            'index' => [
                'byteStart' => $positions[0],
                'byteEnd' => $positions[1],
            ],
            'features' => [
                [
                    '$type' => 'app.bsky.richtext.facet#tag',
                    'tag' => $tag,
                ],
            ],
        ]),
    );
})->with([
    ['A post with a #tag inline', [14, 18], 'tag'],
    ['#tag starting the post', [0, 4], 'tag'],
    ['a post ended by a #tag', [18, 22], 'tag'],
    ['a tag that ends #with! punctuation', [16, 21], 'with'],
    ['a post with #anextremelylongtagwhichisjustunderblueskys64charactertaglimit!', [12, 74], 'anextremelylongtagwhichisjustunderblueskys64charactertaglimit'],
]);

it('doesnt detect non tags', function (string $text) {
    /** @var FacetsResolver */
    $resolver = resolve(FacetsResolver::class);
    $facets = $resolver->resolve(
        bluesky: resolve(BlueskyService::class),
        post: BlueskyPost::make()->text($text),
    );

    expect($facets)->toBe([]);
})->with([
    ['A real#tag must start with space'],
    ['a #1tag can not have a number'],
    ['an #anextremelylongtagwhichisabsolutlyoverblueskys64charactertaglimitisnotaddedasatag'],
]);

it('detects multiple tags', function () {
    /** @var FacetsResolver */
    $resolver = resolve(FacetsResolver::class);
    $facets = $resolver->resolve(
        bluesky: resolve(BlueskyService::class),
        post: BlueskyPost::make()->text('this post has #two #tags'),
    );

    expect($facets)->sequence(
        fn (Expectation $facet) => $facet->toArray()->toBe([
            '$type' => 'app.bsky.richtext.facet',
            'index' => [
                'byteStart' => 14,
                'byteEnd' => 18,
            ],
            'features' => [
                [
                    '$type' => 'app.bsky.richtext.facet#tag',
                    'tag' => 'two',
                ],
            ],
        ]),
        fn (Expectation $facet) => $facet->toArray()->toBe([
            '$type' => 'app.bsky.richtext.facet',
            'index' => [
                'byteStart' => 19,
                'byteEnd' => 24,
            ],
            'features' => [
                [
                    '$type' => 'app.bsky.richtext.facet#tag',
                    'tag' => 'tags',
                ],
            ],
        ]),
    );
});
