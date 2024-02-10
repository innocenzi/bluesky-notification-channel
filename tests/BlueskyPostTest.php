<?php

use NotificationChannels\Bluesky\BlueskyClient;
use NotificationChannels\Bluesky\BlueskyPost;
use NotificationChannels\Bluesky\Tests\Factories\BlueskyClientResponseFactory;

it('can be converted to an array', function () {
    $post = BlueskyPost::make()->text('foo');

    expect($post->toArray())->toBe([
        'text' => 'foo',
        'facets' => [],
    ]);
});

it('has an accessble `text` property', function () {
    $post = BlueskyPost::make()->text('foo');

    expect($post->text)->toBe('foo');
});

it('has an accessble `facets` property', function () {
    $post = BlueskyPost::make()->text('foo');

    expect($post->facets)->toBe([]);
});

it('can generate facets given the `BlueskyClient` instance', function () {
    ray()->showHttpClientRequests();
    BlueskyClientResponseFactory::fake();

    $client = resolve(BlueskyClient::class);

    $post = BlueskyPost::make()
        ->text('Hello @innocenzi.dev')
        ->resolveFacets($client);

    expect($post->toArray())->toBe([
        'text' => 'Hello @innocenzi.dev',
        'facets' => [
            [
                '$type' => 'app.bsky.richtext.facet',
                'index' => [
                    'byteStart' => 6,
                    'byteEnd' => 20,
                ],
                'features' => [
                    [
                        '$type' => 'app.bsky.richtext.facet#mention',
                        'did' => 'did:plc:sa57ykejomjswkuoktilt3sz',
                    ],
                ],
            ],
        ],
    ]);
});
