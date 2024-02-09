<?php

namespace NotificationChannels\Bluesky\Tests\Fixtures;

use Illuminate\Notifications\Notification;
use NotificationChannels\Bluesky\BlueskyPost;

final class TestNotification extends Notification
{
    public function toBluesky(): BlueskyPost
    {
        return BlueskyPost::make()
            ->text('foo');
    }
}
