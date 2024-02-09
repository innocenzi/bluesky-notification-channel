<?php

namespace NotificationChannels\Bluesky\IdentityRepository;

use NotificationChannels\Bluesky\BlueskyIdentity;

interface IdentityRepository
{
    /**
     * Determines whether an identity is stored.
     */
    public function hasIdentity(): bool;

    /**
     * Gets the identity.
     */
    public function getIdentity(): BlueskyIdentity;

    /**
     * Saves the identity.
     */
    public function setIdentity(BlueskyIdentity $identity): void;

    /**
     * Clears the identity.
     */
    public function clearIdentity(): void;
}
