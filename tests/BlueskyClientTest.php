<?php

use NotificationChannels\Bluesky\BlueskyClient;
use NotificationChannels\Bluesky\BlueskyPost;
use NotificationChannels\Bluesky\Exceptions\CouldNotCreatePost;
use NotificationChannels\Bluesky\Exceptions\CouldNotCreateSession;
use NotificationChannels\Bluesky\Exceptions\CouldNotRefreshSession;
use NotificationChannels\Bluesky\Exceptions\CouldNotResolveHandle;
use NotificationChannels\Bluesky\Exceptions\CouldNotUploadBlob;
use NotificationChannels\Bluesky\Tests\Factories\BlueskyClientResponseFactory;

describe('createIdentity', function () {
    it('can create an identity', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::CREATE_SESSION_ENDPOINT => ['handle' => 'uwu'],
        ]);

        /** @var BlueskyClient */
        $client = resolve(BlueskyClient::class);
        $identity = $client->createIdentity();

        expect($identity)->handle->toBe('uwu');
    });

    it('throws when the wrong credentials are given', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::CREATE_SESSION_ENDPOINT => [':status' => 401],
        ]);

        /** @var BlueskyClient */
        $client = resolve(BlueskyClient::class);
        $client->createIdentity();
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
        $client = resolve(BlueskyClient::class);
        $client->createIdentity();
    })->throws(CouldNotCreateSession::class, 'Account is suspended (400, AccountTakedown)');

    it('throws on bad requests', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::CREATE_SESSION_ENDPOINT => [':status' => 400],
        ]);

        /** @var BlueskyClient */
        $client = resolve(BlueskyClient::class);
        $client->createIdentity();
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
        $client = resolve(BlueskyClient::class);
        $client->createIdentity();
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
        $client = resolve(BlueskyClient::class);
        $client->createIdentity();
    })->throws(CouldNotCreateSession::class, 'Token is invalid (400, InvalidToken)');
});

describe('createPost', function () {
    it('can create a post', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::CREATE_RECORD_ENDPOINT => ['uri' => 'foo'],
        ]);

        /** @var BlueskyClient */
        $client = resolve(BlueskyClient::class);
        $identity = $client->createIdentity();
        $response = $client->createPost(
            identity: $identity,
            post: BlueskyPost::make()->text('Hello, world!'),
        );

        expect($response)->toBe('foo');
    });

    it('can create a post with an embed', function () {
        BlueskyClientResponseFactory::fake([
            'https://cardyb.bsky.app/v1/extract*' => [
                'error' => '',
                'url' => 'https://innocenzi.dev',
                'title' => 'Enzo Innocenzi - Software developer',
                'description' => 'I am too lazy to copy it',
                'image' => 'https://cardyb.bsky.app/v1/image?url=https%3A%2F%2Finnocenzi.dev%2Fog.jpg',
            ],
        ]);

        /** @var BlueskyClient */
        $client = resolve(BlueskyClient::class);
        $identity = $client->createIdentity();
        $response = $client->createPost(
            identity: $identity,
            post: BlueskyPost::make()->text('Hello from https://innocenzi.dev'),
        );

        expect($response)->toBe('at://did:plc:sa57ykejomjswkuoktilt3sz/app.bsky.feed.post/3kkxqb634qt2e');
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
        $client = resolve(BlueskyClient::class);
        $client->createPost(
            identity: $client->createIdentity(),
            post: BlueskyPost::make()->text('Hello, world!'),
        );
    })->throws(CouldNotCreatePost::class, 'Token is expired (400, ExpiredToken)');
});

describe('refreshIdentity', function () {
    it('can refresh an identity', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::REFRESH_SESSION_ENDPOINT => ['handle' => 'owo'],
        ]);

        /** @var BlueskyClient */
        $client = resolve(BlueskyClient::class);
        $identity = $client->createIdentity();
        $response = $client->refreshIdentity($identity);

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
        $client = resolve(BlueskyClient::class);
        $client->refreshIdentity(
            identity: $client->createIdentity(),
        );
    })->throws(CouldNotRefreshSession::class, 'Token is expired (400, ExpiredToken)');
});

describe('resolveHandle', function () {
    it('can resolve a handle to a did', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::RESOLVE_HANDLE_ENDPOINT => ['did' => 'did:example:123'],
        ]);

        /** @var BlueskyClient */
        $client = resolve(BlueskyClient::class);
        $response = $client->resolveHandle(handle: 'uwu');

        expect($response)->toBe('did:example:123');
    });

    it('throws when the refresh token is expired', function () {
        BlueskyClientResponseFactory::fake([
            BlueskyClient::RESOLVE_HANDLE_ENDPOINT => [
                ':status' => 400,
                'error' => 'ExpiredToken',
                'message' => null,
            ],
        ]);

        /** @var BlueskyClient */
        $client = resolve(BlueskyClient::class);
        $client->resolveHandle(handle: 'uwu');
    })->throws(CouldNotResolveHandle::class, 'Token is expired (400, ExpiredToken)');
});

describe('uploadBlob', function () {
    it('throws when no file is provided', function () {
        BlueskyClientResponseFactory::fake();

        /** @var BlueskyClient */
        $client = resolve(BlueskyClient::class);
        $client->uploadBlob(
            identity: $client->createIdentity(),
            pathOrUrl: '',
        );
    })->throws(CouldNotUploadBlob::class);

    it('can upload a blob via an external url', function () {
        BlueskyClientResponseFactory::fake();

        /** @var BlueskyService */
        $client = resolve(BlueskyClient::class);
        $response = $client->uploadBlob(
            identity: $client->createIdentity(),
            pathOrUrl: 'https://cardyb.bsky.app/v1/image?url=https%3A%2F%2Fwww.docs.bsky.app%2Fimg%2Fsocial-card-default.png',
        );

        expect($response->blob)->toBe([
            '$type' => 'blob',
            'ref' => [
                '$link' => 'bafkreialypbxslmeod6vvjskyzujexd4ow6huuil354ov66zgqp23hwdlq',
            ],
            'mimeType' => 'multipart/form-data',
            'size' => 17066,
        ]);
    });
});
