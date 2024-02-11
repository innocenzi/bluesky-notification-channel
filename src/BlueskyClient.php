<?php

namespace NotificationChannels\Bluesky;

use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\Response;
use NotificationChannels\Bluesky\Embeds\EmbedResolver;
use NotificationChannels\Bluesky\Exceptions\CouldNotCreatePost;
use NotificationChannels\Bluesky\Exceptions\CouldNotCreateSession;
use NotificationChannels\Bluesky\Exceptions\CouldNotRefreshSession;
use NotificationChannels\Bluesky\Exceptions\CouldNotResolveHandle;
use NotificationChannels\Bluesky\Exceptions\CouldNotUploadBlob;
use ValueError;

final class BlueskyClient
{
    public const DEFAULT_BASE_URL = 'https://bsky.social/xrpc';
    public const REFRESH_SESSION_ENDPOINT = 'com.atproto.server.refreshSession';
    public const CREATE_SESSION_ENDPOINT = 'com.atproto.server.createSession';
    public const CREATE_RECORD_ENDPOINT = 'com.atproto.repo.createRecord';
    public const UPLOAD_BLOB_ENDPOINT = 'com.atproto.repo.uploadBlob';
    public const RESOLVE_HANDLE_ENDPOINT = 'com.atproto.identity.resolveHandle';

    public function __construct(
        protected readonly HttpClient $httpClient,
        protected readonly EmbedResolver $embedResolver,
        protected readonly string $baseUrl,
        protected readonly string $username,
        protected readonly string $password,
    ) {
    }

    public function resolveHandle(string $handle): string
    {
        $response = $this->httpClient
            ->asJson()
            ->get("{$this->baseUrl}/" . self::RESOLVE_HANDLE_ENDPOINT, [
                'handle' => $handle,
            ]);

        $this->ensureResponseSucceeded($response, CouldNotResolveHandle::class);

        return $response->json('did');
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
            ->asForm()
            ->withHeader('Authorization', "Bearer {$identity->refreshJwt}")
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

    public function createPost(BlueskyIdentity $identity, BlueskyPost $post): string
    {
        $response = $this->httpClient
            ->asJson()
            ->withHeader('Authorization', "Bearer {$identity->accessJwt}")
            ->post("{$this->baseUrl}/" . self::CREATE_RECORD_ENDPOINT, [
                'repo' => $identity->handle,
                'collection' => 'app.bsky.feed.post',
                'record' => [
                    'createdAt' => now()->toIso8601ZuluString(),
                    ...$post->toArray(),
                ],
            ]);

        $this->ensureResponseSucceeded($response, CouldNotCreatePost::class);

        return $response->json('uri');
    }

    public function uploadBlob(BlueskyIdentity $identity, string $pathOrUrl): Blob
    {
        $content = null;

        try {
            $content = @file_get_contents($pathOrUrl);
        } catch (ValueError) {
            throw CouldNotUploadBlob::couldNotLoadImage();
        }

        if ($content === false) {
            $content = $this->httpClient->get($pathOrUrl)->body();
        }

        if (!$content) {
            throw CouldNotUploadBlob::couldNotLoadImage();
        }

        $response = $this->httpClient
            ->contentType('image/*')
            ->withHeader('Authorization', "Bearer {$identity->accessJwt}")
            ->send('POST', "{$this->baseUrl}/" . self::UPLOAD_BLOB_ENDPOINT, [
                'body' => $content,
            ]);

        $this->ensureResponseSucceeded($response, CouldNotUploadBlob::class);

        return new Blob(
            blob: $response->json('blob'),
        );
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
