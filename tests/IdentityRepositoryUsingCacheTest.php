<?php

use Illuminate\Cache\Repository as CacheRepository;
use NotificationChannels\Bluesky\Exceptions\NoBlueskyIdentityFound;
use NotificationChannels\Bluesky\IdentityRepository\IdentityRepositoryUsingCache;
use NotificationChannels\Bluesky\Tests\Factories\BlueskyClientResponseFactory;

beforeEach(function () {
    app()->when(IdentityRepositoryUsingCache::class)
        ->needs('$key')
        ->give(IdentityRepositoryUsingCache::DEFAULT_CACHE_KEY);
});

test('`setIdentity` stores an identity in the cache', function () {
    $identity = BlueskyClientResponseFactory::createIdentity();

    /** @var IdentityRepositoryUsingCache */
    $identityRepository = resolve(IdentityRepositoryUsingCache::class);
    $identityRepository->setIdentity($identity);

    /** @var CacheRepository */
    $cache = resolve(CacheRepository::class);
    expect($cache->get(IdentityRepositoryUsingCache::DEFAULT_CACHE_KEY))->toBe($identity);
});

test('`getIdentity` returns the stored identity', function () {
    $identity = BlueskyClientResponseFactory::createIdentity();

    /** @var IdentityRepositoryUsingCache */
    $identityRepository = resolve(IdentityRepositoryUsingCache::class);
    $identityRepository->setIdentity($identity);

    expect($identityRepository->getIdentity())->toBe($identity);
});

test('`getIdentity` throws when no identity is stored', function () {
    /** @var IdentityRepositoryUsingCache */
    $identityRepository = resolve(IdentityRepositoryUsingCache::class);

    expect(fn () => $identityRepository->getIdentity())
        ->toThrow(NoBlueskyIdentityFound::class);
});

test('`clearIdentity` clears the stored identity', function () {
    $identity = BlueskyClientResponseFactory::createIdentity();

    /** @var IdentityRepositoryUsingCache */
    $identityRepository = resolve(IdentityRepositoryUsingCache::class);
    $identityRepository->setIdentity($identity);

    $identityRepository->clearIdentity();

    expect(fn () => $identityRepository->getIdentity())
        ->toThrow(NoBlueskyIdentityFound::class);
});

test('`hasIdentity` returns `true` when an identity is stored', function () {
    $identity = BlueskyClientResponseFactory::createIdentity();

    /** @var IdentityRepositoryUsingCache */
    $identityRepository = resolve(IdentityRepositoryUsingCache::class);
    $identityRepository->setIdentity($identity);

    expect($identityRepository->hasIdentity())->toBeTrue();
});
