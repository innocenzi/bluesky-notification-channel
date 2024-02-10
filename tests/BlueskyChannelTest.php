<?php

use NotificationChannels\Bluesky\BlueskyChannel;
use NotificationChannels\Bluesky\BlueskyClient;
use NotificationChannels\Bluesky\BlueskyService;
use NotificationChannels\Bluesky\Exceptions\NoBlueskyChannel;
use NotificationChannels\Bluesky\Tests\Factories\BlueskyClientResponseFactory;
use NotificationChannels\Bluesky\Tests\Fixtures\TestNotifiable;
use NotificationChannels\Bluesky\Tests\Fixtures\TestNotification;
use NotificationChannels\Bluesky\Tests\Fixtures\TestNotificationWithMention;
use NotificationChannels\Bluesky\Tests\Fixtures\TestNotificationWithoutChannel;

test('posts can be created', function () {
    BlueskyClientResponseFactory::fake([
        BlueskyClient::CREATE_RECORD_ENDPOINT => ['uri' => 'foo'],
    ]);

    /** @var BlueskyService */
    $service = resolve(BlueskyService::class);

    $channel = new BlueskyChannel($service);
    $response = $channel->send(new TestNotifiable(), new TestNotification());

    expect($response)->toBe('foo');
});

test('posts with mentions can be created', function () {
    BlueskyClientResponseFactory::fake([
        BlueskyClient::CREATE_RECORD_ENDPOINT => ['uri' => 'at://did:plc:z72i7hdynmk6r22z27h6tvur/app.bsky.feed.post/3jt6walwmos2y'],
    ]);

    /** @var BlueskyService */
    $service = resolve(BlueskyService::class);

    $channel = new BlueskyChannel($service);
    $response = $channel->send(new TestNotifiable(), new TestNotificationWithMention());

    expect($response)->toBe('at://did:plc:z72i7hdynmk6r22z27h6tvur/app.bsky.feed.post/3jt6walwmos2y');
});

test('an exception is thrown if the notification does not have a `toBluesky` method', function () {
    $channel = new BlueskyChannel(
        bluesky: resolve(BlueskyService::class),
    );

    $channel->send(new TestNotifiable(), new TestNotificationWithoutChannel());
})->throws(NoBlueskyChannel::class, 'The `toBluesky` method must be defined on [NotificationChannels\Bluesky\Tests\Fixtures\TestNotificationWithoutChannel].');
