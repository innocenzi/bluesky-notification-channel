<?php

namespace NotificationChannels\Bluesky\IdentityRepository;

use Illuminate\Cache\Repository as CacheRepository;
use NotificationChannels\Bluesky\BlueskyIdentity;
use NotificationChannels\Bluesky\Exceptions\NoBlueskyIdentityFound;

class IdentityRepositoryUsingCache implements IdentityRepository
{
    public const DEFAULT_CACHE_KEY = 'bluesky-notification-channel:identity';

    public function __construct(
        private readonly CacheRepository $cache,
        private readonly string $key,
    ) {
    }

    public function clearIdentity(): void
    {
        $this->cache->forget($this->key);
    }

    public function hasIdentity(): bool
    {
        return $this->cache->get($this->key) instanceof BlueskyIdentity;
    }

    public function getIdentity(): BlueskyIdentity
    {
        if (!$this->hasIdentity()) {
            throw NoBlueskyIdentityFound::create();
        }

        return $this->cache->get($this->key);
    }

    public function setIdentity(BlueskyIdentity $identity): void
    {
        $this->cache->set(
            key: $this->key,
            value: $identity,
        );
    }
}
