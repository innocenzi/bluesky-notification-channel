<?php

namespace NotificationChannels\Bluesky;

use Illuminate\Notifications\Notification;
use NotificationChannels\Bluesky\Exceptions\NoBlueskyChannel;

final class BlueskyChannel
{
    public function __construct(
        protected readonly BlueskyService $bluesky,
    ) {
    }

    public function send(mixed $notifiable, Notification $notification): string
    {
        if (!method_exists($notification, 'toBluesky')) {
            throw NoBlueskyChannel::create(\get_class($notification));
        }

        return $this->bluesky->createPost(
            text: $notification->toBluesky($notifiable),
        );
    }
}
