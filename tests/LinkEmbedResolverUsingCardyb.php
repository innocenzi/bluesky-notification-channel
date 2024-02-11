<?php

use NotificationChannels\Bluesky\BlueskyPost;
use NotificationChannels\Bluesky\BlueskyService;
use NotificationChannels\Bluesky\Embeds\EmbedResolver;
use NotificationChannels\Bluesky\Embeds\External;
use NotificationChannels\Bluesky\Facets\Facet;
use NotificationChannels\Bluesky\Facets\Link;
use NotificationChannels\Bluesky\Tests\Factories\HttpResponsesFactory;

it('resolves the first website embed from a post', function () {
    HttpResponsesFactory::fake();

    /** @var EmbedResolver */
    $embedResolver = resolve(EmbedResolver::class);

    $embed = $embedResolver->resolve(
        bluesky: resolve(BlueskyService::class),
        post: BlueskyPost::make()
            ->text('Hi from https://innocenzi.dev')
            ->facet(new Facet(
                range: [8, 29],
                features: [new Link(uri: 'https://innocenzi.dev')],
            )),
    );

    expect($embed)
        ->toBeInstanceOf(External::class)
        ->uri->toBe('https://innocenzi.dev');
});
