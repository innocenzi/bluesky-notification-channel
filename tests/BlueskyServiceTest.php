<?php

use NotificationChannels\Bluesky\BlueskyClient;
use NotificationChannels\Bluesky\BlueskyPost;
use NotificationChannels\Bluesky\BlueskyService;
use NotificationChannels\Bluesky\Tests\Factories\BlueskyClientResponseFactory;

test('it can create a post with a string', function () {
    BlueskyClientResponseFactory::fake([
        BlueskyClient::CREATE_RECORD_ENDPOINT => ['uri' => 'foo'],
    ]);

    /** @var BlueskyService */
    $service = resolve(BlueskyService::class);
    $response = $service->createPost('Hello');

    expect($response)->toBe('foo');

    BlueskyClientResponseFactory::assertSent(['record' => ['text' => 'Hello']]);
});

test('it can create a post with a `BlueskyPost` instance', function () {
    BlueskyClientResponseFactory::fake([
        BlueskyClient::CREATE_RECORD_ENDPOINT => ['uri' => 'foo'],
    ]);

    /** @var BlueskyService */
    $service = resolve(BlueskyService::class);
    $response = $service->createPost(BlueskyPost::make()->text('Hello'));

    expect($response)->toBe('foo');

    BlueskyClientResponseFactory::assertSent(['record' => ['text' => 'Hello']]);
});
