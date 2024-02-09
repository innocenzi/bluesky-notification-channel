<?php

namespace NotificationChannels\Bluesky;

use NotificationChannels\Bluesky\IdentityRepository\IdentityRepository;
use NotificationChannels\Bluesky\SessionManager\SessionManager;

final class BlueskyService
{
    public function __construct(
        protected readonly BlueskyClient $client,
        protected readonly IdentityRepository $identityRepository,
        protected readonly SessionManager $sessionManager,
    ) {
    }

    public function createPost(BlueskyPost|string $text): string
    {
        return $this->client->createPost(
            identity: $this->sessionManager->getIdentity(),
            text: $text,
        );
    }
}
