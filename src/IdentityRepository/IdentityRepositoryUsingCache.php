<?php

namespace NotificationChannels\Bluesky\IdentityRepository;

use Illuminate\Cache\Repository as CacheRepository;
use NotificationChannels\Bluesky\BlueskyIdentity;
use NotificationChannels\Bluesky\Exceptions\NoBlueskyIdentityFound;

class IdentityRepositoryUsingCache implements IdentityRepository
{
    public const CACHE_KEY = 'bluesky-notification-channel:identity';

    public function __construct(
        private readonly CacheRepository $cache,
    ) {
    }

    public function clearIdentity(): void
    {
        $this->cache->forget(static::CACHE_KEY);
    }

    public function hasIdentity(): bool
    {
        return $this->cache->get(static::CACHE_KEY) instanceof BlueskyIdentity;
    }

    public function getIdentity(): BlueskyIdentity
    {
        if (!$this->hasIdentity()) {
            throw NoBlueskyIdentityFound::create();
        }

        return $this->cache->get(static::CACHE_KEY);
    }

    public function setIdentity(BlueskyIdentity $identity): void
    {
        $this->cache->set(
            key: static::CACHE_KEY,
            value: $identity,
        );
    }
}
