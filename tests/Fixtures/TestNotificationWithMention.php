<?php

namespace NotificationChannels\Bluesky\Tests\Fixtures;

use Illuminate\Notifications\Notification;
use NotificationChannels\Bluesky\BlueskyPost;

final class TestNotificationWithMention extends Notification
{
    public function toBluesky(): BlueskyPost
    {
        return BlueskyPost::make()
            ->text('Hello @innocenzi.dev and @hylmew.net');
    }
}
