<?php

use NotificationChannels\Bluesky\BlueskyClient;
use NotificationChannels\Bluesky\Exceptions\CouldNotCreatePost;
use NotificationChannels\Bluesky\Exceptions\CouldNotCreateSession;
use NotificationChannels\Bluesky\Exceptions\CouldNotRefreshSession;
use NotificationChannels\Bluesky\Tests\Factories\BlueskyClientResponseFactory;

describe('createIdentity', function () {
    it('can create an identity', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::CREATE_SESSION_ENDPOINT => ['handle' => 'uwu'],
        ]);

        /** @var BlueskyClient */
        $service = resolve(BlueskyClient::class);
        $identity = $service->createIdentity();

        expect($identity)->handle->toBe('uwu');
    });

    it('throws when the wrong credentials are given', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::CREATE_SESSION_ENDPOINT => [':status' => 401],
        ]);

        /** @var BlueskyClient */
        $service = resolve(BlueskyClient::class);
        $service->createIdentity();
    })->throws(CouldNotCreateSession::class, 'Could not create session (401)');

    it('throws when the account has been taken down', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::CREATE_SESSION_ENDPOINT => [
                ':status' => 400,
                'error' => 'AccountTakedown',
                'message' => null,
            ],
        ]);

        /** @var BlueskyClient */
        $service = resolve(BlueskyClient::class);
        $service->createIdentity();
    })->throws(CouldNotCreateSession::class, 'Account is suspended (400, AccountTakedown)');

    it('throws on bad requests', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::CREATE_SESSION_ENDPOINT => [':status' => 400],
        ]);

        /** @var BlueskyClient */
        $service = resolve(BlueskyClient::class);
        $service->createIdentity();
    })->throws(CouldNotCreateSession::class, 'Could not create session (400)');

    it('throws when token is expired', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::CREATE_SESSION_ENDPOINT => [
                ':status' => 400,
                'error' => 'ExpiredToken',
                'message' => null,
            ],
        ]);

        /** @var BlueskyClient */
        $service = resolve(BlueskyClient::class);
        $service->createIdentity();
    })->throws(CouldNotCreateSession::class, 'Token is expired (400, ExpiredToken)');

    it('throws when token is invalid', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::CREATE_SESSION_ENDPOINT => [
                ':status' => 400,
                'error' => 'InvalidToken',
                'message' => null,
            ],
        ]);

        /** @var BlueskyClient */
        $service = resolve(BlueskyClient::class);
        $service->createIdentity();
    })->throws(CouldNotCreateSession::class, 'Token is invalid (400, InvalidToken)');
});

describe('createPost', function () {
    it('can create a post', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::CREATE_RECORD_ENDPOINT => ['uri' => 'foo'],
        ]);

        /** @var BlueskyClient */
        $service = resolve(BlueskyClient::class);
        $identity = $service->createIdentity();
        $response = $service->createPost($identity, 'Hello, world!');

        expect($response)->toBe('foo');
    });

    it('throws when token is expired', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::CREATE_RECORD_ENDPOINT => [
                ':status' => 400,
                'error' => 'ExpiredToken',
                'message' => null,
            ],
        ]);

        /** @var BlueskyClient */
        $service = resolve(BlueskyClient::class);
        $service->createPost(
            identity: $service->createIdentity(),
            text: 'Hello, world!',
        );
    })->throws(CouldNotCreatePost::class, 'Token is expired (400, ExpiredToken)');
});

describe('refreshIdentity', function () {
    it('can refresh an identity', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::REFRESH_SESSION_ENDPOINT => ['handle' => 'owo'],
        ]);

        /** @var BlueskyClient */
        $service = resolve(BlueskyClient::class);
        $identity = $service->createIdentity();
        $response = $service->refreshIdentity($identity);

        expect($response)->handle->toBe('owo');
    });

    it('throws when the refresh token is expired', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::REFRESH_SESSION_ENDPOINT => [
                ':status' => 400,
                'error' => 'ExpiredToken',
                'message' => null,
            ],
        ]);

        /** @var BlueskyClient */
        $service = resolve(BlueskyClient::class);
        $service->refreshIdentity(
            identity: $service->createIdentity(),
        );
    })->throws(CouldNotRefreshSession::class, 'Token is expired (400, ExpiredToken)');
});
