<?php

use NotificationChannels\Bluesky\BlueskyClient;
use NotificationChannels\Bluesky\BlueskyPost;
use NotificationChannels\Bluesky\BlueskyService;
use NotificationChannels\Bluesky\Embeds\EmbedResolver;
use NotificationChannels\Bluesky\Embeds\External;
use NotificationChannels\Bluesky\RichText\Facets\Facet;
use NotificationChannels\Bluesky\Tests\Factories\BlueskyClientResponseFactory;

it('resolves the first website embed from a post', function () {
    BlueskyClientResponseFactory::fake([
        'https://cardyb.bsky.app/v1/extract*' => [
            'error' => '',
            'url' => 'https://innocenzi.dev',
            'title' => 'Enzo Innocenzi - Software developer',
            'description' => 'I am too lazy to copy it',
            'image' => 'https://cardyb.bsky.app/v1/image?url=https%3A%2F%2Finnocenzi.dev%2Fog.jpg',
        ],
    ]);

    /** @var BlueskyService */
    $service = resolve(BlueskyService::class);
    /** @var BlueskyClient */
    $client = resolve(BlueskyClient::class);
    /** @var EmbedResolver */
    $resolver = resolve(EmbedResolver::class);

    $embed = $resolver->resolve(
        bluesky: $service,
        post: BlueskyPost::make()
            ->text($text = 'Hi from https://innocenzi.dev')
            ->facets(Facet::resolveFacets($text, $client)),
    );

    expect($embed)
        ->toBeInstanceOf(External::class)
        ->uri->toBe('https://innocenzi.dev');
});
