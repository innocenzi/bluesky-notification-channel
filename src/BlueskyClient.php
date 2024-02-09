<?php

namespace NotificationChannels\Bluesky;

use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\Response;
use NotificationChannels\Bluesky\Exceptions\CouldNotCreatePost;
use NotificationChannels\Bluesky\Exceptions\CouldNotCreateSession;
use NotificationChannels\Bluesky\Exceptions\CouldNotRefreshSession;

final class BlueskyClient
{
    public const DEFAULT_BASE_URL = 'https://bsky.social/xrpc';
    public const REFRESH_SESSION_ENDPOINT = 'com.atproto.server.refreshSession';
    public const CREATE_SESSION_ENDPOINT = 'com.atproto.server.createSession';
    public const CREATE_RECORD_ENDPOINT = 'com.atproto.repo.createRecord';

    public function __construct(
        protected readonly HttpClient $httpClient,
        protected readonly string $baseUrl,
        protected readonly string $username,
        protected readonly string $password,
    ) {
    }

    public function createIdentity(): BlueskyIdentity
    {
        $response = $this->httpClient
            ->asJson()
            ->post("{$this->baseUrl}/" . self::CREATE_SESSION_ENDPOINT, [
                'identifier' => $this->username,
                'password' => $this->password,
            ]);

        $this->ensureResponseSucceeded($response, CouldNotCreateSession::class);

        return new BlueskyIdentity(
            did: $response->json('did'),
            handle: $response->json('handle'),
            email: $response->json('email'),
            accessJwt: $response->json('accessJwt'),
            refreshJwt: $response->json('refreshJwt'),
        );
    }

    public function refreshIdentity(BlueskyIdentity $identity): BlueskyIdentity
    {
        $response = $this->httpClient
            ->withHeader('Authorization', "Bearer {$identity->refreshJwt}")
            ->asForm()
            ->post("{$this->baseUrl}/" . self::REFRESH_SESSION_ENDPOINT);

        $this->ensureResponseSucceeded($response, CouldNotRefreshSession::class);

        return new BlueskyIdentity(
            did: $response->json('did'),
            handle: $response->json('handle'),
            email: $identity->email, // this does not get returned in the refresh response
            accessJwt: $response->json('accessJwt'),
            refreshJwt: $response->json('refreshJwt'),
        );
    }

    public function createPost(BlueskyIdentity $identity, BlueskyPost|string $text): string
    {
        $response = $this->httpClient
            ->asJson()
            ->withHeader('Authorization', "Bearer {$identity->accessJwt}")
            ->post("{$this->baseUrl}/" . self::CREATE_RECORD_ENDPOINT, [
                'repo' => $identity->handle,
                'collection' => 'app.bsky.feed.post',
                'record' => [
                    'text' => (string) $text,
                    'createdAt' => now()->toIso8601ZuluString(),
                ],
            ]);

        $this->ensureResponseSucceeded($response, CouldNotCreatePost::class);

        return $response->json('uri');
    }

    private function ensureResponseSucceeded(Response $response, string $errorClass): void
    {
        if ($response->ok()) {
            return;
        }

        throw $errorClass::create(
            status: $response->status(),
            error: $response->json('error'),
            message: $response->json('message'),
        );
    }
}
