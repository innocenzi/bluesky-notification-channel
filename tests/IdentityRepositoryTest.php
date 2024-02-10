<?php

use NotificationChannels\Bluesky\IdentityRepository\IdentityRepository;
use NotificationChannels\Bluesky\IdentityRepository\IdentityRepositoryUsingCache;

test('the default implementation is `IdentityRepositoryUsingCache`', function () {
    expect(resolve(IdentityRepository::class))->toBeInstanceOf(IdentityRepositoryUsingCache::class);
});
