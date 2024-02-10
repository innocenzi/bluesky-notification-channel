<?php

namespace NotificationChannels\Bluesky;

use NotificationChannels\Bluesky\IdentityRepository\IdentityRepository;

/**
 * Ensures that the identity is always up-to-date.
 */
final class SessionManager
{
    public function __construct(
        private readonly BlueskyClient $client,
        private readonly IdentityRepository $identityRepository,
    ) {
    }

    /**
     * Gets an updated identity.
     */
    public function getIdentity(): BlueskyIdentity
    {
        $this->ensureHasIdentity();
        $this->refreshIdentity();

        return $this->identityRepository->getIdentity();
    }

    /**
     * Ensures an identity exists.
     */
    private function ensureHasIdentity(): void
    {
        if ($this->identityRepository->hasIdentity()) {
            return;
        }

        $this->identityRepository->setIdentity(
            identity: $this->client->createIdentity(),
        );
    }

    /**
     * Refreshes the existing identity.
     */
    private function refreshIdentity(): void
    {
        $identity = $this->client->refreshIdentity(
            identity: $this->identityRepository->getIdentity(),
        );

        $this->identityRepository->setIdentity($identity);
    }
}
