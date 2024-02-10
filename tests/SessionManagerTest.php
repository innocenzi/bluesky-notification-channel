<?php

use NotificationChannels\Bluesky\BlueskyClient;
use NotificationChannels\Bluesky\SessionManager;
use NotificationChannels\Bluesky\Tests\Factories\BlueskyClientResponseFactory;

test('`getIdentity` generates an identity when none exist', function () {
    BlueskyClientResponseFactory::fake([
        BlueskyClient::CREATE_SESSION_ENDPOINT => ['handle' => 'owo'],
        BlueskyClient::REFRESH_SESSION_ENDPOINT => ['handle' => 'uwu'],
    ]);

    /** @var SessionManager */
    $session = resolve(SessionManager::class);
    $identity = $session->getIdentity();

    expect($identity)->handle->toBe('uwu');
});

// Note: this is the same test as above, but the test above
// will need to be changed if/when we decide not to refresh
// sessions if they are not close to their expiration date
test('`getIdentity` refreshes the session automatically', function () {
    BlueskyClientResponseFactory::fake([
        BlueskyClient::CREATE_SESSION_ENDPOINT => ['handle' => 'owo'],
        BlueskyClient::REFRESH_SESSION_ENDPOINT => ['handle' => 'uwu'],
    ]);

    /** @var SessionManager */
    $session = resolve(SessionManager::class);
    $identity = $session->getIdentity();

    expect($identity)->handle->toBe('uwu');
});
