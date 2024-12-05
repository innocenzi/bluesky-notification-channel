<?php

use NotificationChannels\Bluesky\BlueskyClient;
use NotificationChannels\Bluesky\BlueskyPost;
use NotificationChannels\Bluesky\BlueskyService;
use NotificationChannels\Bluesky\Embeds\External;
use NotificationChannels\Bluesky\Embeds\LinkEmbedResolverUsingCardyb;
use NotificationChannels\Bluesky\Tests\Factories\HttpResponsesFactory;

test('it can create a post with a string', function () {
    HttpResponsesFactory::fake([
        BlueskyClient::CREATE_RECORD_ENDPOINT => ['uri' => 'foo'],
    ]);

    /** @var BlueskyService */
    $service = resolve(BlueskyService::class);
    $response = $service->createPost('Hello');

    expect($response)->toBe('foo');

    HttpResponsesFactory::assertSent(['record' => ['text' => 'Hello']]);
});

test('it can create a post with a `BlueskyPost` instance', function () {
    HttpResponsesFactory::fake([
        BlueskyClient::CREATE_RECORD_ENDPOINT => ['uri' => 'foo'],
    ]);

    /** @var BlueskyService */
    $service = resolve(BlueskyService::class);
    $response = $service->createPost(BlueskyPost::make()->text('Hello'));

    expect($response)->toBe('foo');

    HttpResponsesFactory::assertSent(['record' => ['text' => 'Hello']]);
});

test('it can upload a blob', function () {
    HttpResponsesFactory::fake();

    /** @var BlueskyService */
    $service = resolve(BlueskyService::class);
    $response = $service->uploadBlob('https://cardyb.bsky.app/v1/image?url=https%3A%2F%2Fwww.docs.bsky.app%2Fimg%2Fsocial-card-default.png');

    expect($response->blob)->toBe([
        '$type' => 'blob',
        'ref' => [
            '$link' => 'bafkreialypbxslmeod6vvjskyzujexd4ow6huuil354ov66zgqp23hwdlq',
        ],
        'mimeType' => 'multipart/form-data',
        'size' => 17066,
    ]);
})->skip('Needs updating');

test('the embed url specified in the post is resolved', function () {
    HttpResponsesFactory::fake([
        LinkEmbedResolverUsingCardyb::ENDPOINT => [
            'error' => '',
            'url' => 'https://google.fr',
            'title' => 'Google',
            'description' => 'I am too lazy to copy it',
        ]
    ]);

    $post = BlueskyPost::make()
        ->withoutAutomaticEmbeds()
        ->embedUrl('https://google.fr');

    /** @var BlueskyService */
    $service = resolve(BlueskyService::class);
    $service->resolvePost($post);

    expect($post->embed)
        ->toBeInstanceOf(External::class)
        ->title->toBe('Google');
});

test('the embed specified in the post is used', function () {
    $post = BlueskyPost::make()
        ->withoutAutomaticEmbeds()
        ->embed(new External('https://foo.fr', 'Foo', 'Bar'));

    /** @var BlueskyService */
    $service = resolve(BlueskyService::class);
    $service->resolvePost($post);

    expect($post->embed)
        ->toBeInstanceOf(External::class)
        ->uri->toBe('https://foo.fr')
        ->title->toBe('Foo');
});
