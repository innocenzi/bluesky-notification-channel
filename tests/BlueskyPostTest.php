<?php

use NotificationChannels\Bluesky\BlueskyPost;
use NotificationChannels\Bluesky\RichText\Facets\Facet;
use NotificationChannels\Bluesky\RichText\Facets\Features\Mention;

it('can be converted to an array', function () {
    $post = BlueskyPost::make()
        ->text('foo')
        ->facet(new Facet(
            range: [6, 20],
            features: [
                new Mention('did:plc:sa57ykejomjswkuoktilt3sz'),
            ],
        ));

    expect($post->toArray())->toBe([
        'text' => 'foo',
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

it('has an accessble `text` property', function () {
    $post = BlueskyPost::make()->text('foo');

    expect($post->text)->toBe('foo');
});

it('has an accessble `facets` property', function () {
    $post = BlueskyPost::make()->text('foo');

    expect($post->facets)->toBe([]);
});
