<?php

use NotificationChannels\Bluesky\BlueskyPost;
use NotificationChannels\Bluesky\Embeds\External;
use NotificationChannels\Bluesky\Facets\Facet;
use NotificationChannels\Bluesky\Facets\Mention;

it('can be converted to an array', function () {
    $post = BlueskyPost::make()
        ->text('foo')
        ->language(['en-US'])
        ->embed(new External(
            uri: 'https://innocenzi.dev',
            title: 'Enzo Innocenzi - Software developer',
            description: 'I am too lazy',
            thumb: [
                '$type' => 'blob',
                'ref' => [
                    '$link' => 'bafkreiash5eihfku2jg4skhyh5kes7j5d5fd6xxloaytdywcvb3r3zrzhu',
                ],
                'mimeType' => 'image/png',
                'size' => 23527,
            ],
        ))
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
        'embed' => [
            '$type' => 'app.bsky.embed.external',
            'external' => [
                'uri' => 'https://innocenzi.dev',
                'title' => 'Enzo Innocenzi - Software developer',
                'description' => 'I am too lazy',
                'thumb' => [
                    '$type' => 'blob',
                    'ref' => [
                        '$link' => 'bafkreiash5eihfku2jg4skhyh5kes7j5d5fd6xxloaytdywcvb3r3zrzhu',
                    ],
                    'mimeType' => 'image/png',
                    'size' => 23527,
                ],
            ],
        ],
        'langs' => ['en-US'],
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
