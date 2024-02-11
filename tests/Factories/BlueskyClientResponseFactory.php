<?php

namespace NotificationChannels\Bluesky\Tests\Factories;

use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use NotificationChannels\Bluesky\BlueskyClient;
use NotificationChannels\Bluesky\BlueskyIdentity;

final class BlueskyClientResponseFactory
{
    private static array $sentRequests = [];

    private static array $fakeCreateSessionResponse = [
        'did' => 'did:plc:sa57ykejomjswkuoktilt3sz',
        'handle' => 'innocenzi.dev',
        'email' => 'enzo@innocenzi.dev',
        'emailConfirmed' => true,
        'accessJwt' => 'foo-bar',
        'refreshJwt' => 'bar-foo',
    ];

    private static array $fakeRefreshSessionResponse = [
        'accessJwt' => 'foo-bar',
        'refreshJwt' => 'bar-foo',
        'handle' => 'innocenzi.dev',
        'did' => 'did:plc:sa57ykejomjswkuoktilt3sz',
        'didDoc' => [],
    ];

    private static array $fakeResolveHandleResponse = [
        'did' => 'did:plc:sa57ykejomjswkuoktilt3sz',
    ];

    private static array $fakeCreatePostResponse = [
        'uri' => 'at://did:plc:sa57ykejomjswkuoktilt3sz/app.bsky.feed.post/3kkxqb634qt2e',
        'cid' => 'bafyreidbmavpfhe7d7e7levliaeprqyknh6pwyauw6qavtubfyetnzug7y', // hash of the text
    ];

    private static array $fakeUploadBlobResponse = [
        'blob' => [
            '$type' => 'blob',
            'ref' => [
                '$link' => 'bafkreialypbxslmeod6vvjskyzujexd4ow6huuil354ov66zgqp23hwdlq',
            ],
            'mimeType' => 'multipart/form-data',
            'size' => 17066,
        ],
    ];

    public static function assertSent(array $expected): void
    {
        // TODO: must check that somewhere in $sentRequests, the properties and values of $expected are included
        // order doesn't matter, and missing properties of $expected don't matter either
        test()->markTestIncomplete('Assertion not implemented');
    }

    public static function fake(array $endpoints = []): void
    {
        static::$sentRequests = [];

        Event::listen(RequestSending::class, function (RequestSending $event) {
            static::$sentRequests[] = json_decode($event->request->body(), associative: true);
        });

        $endpoints[BlueskyClient::CREATE_SESSION_ENDPOINT] ??= [];
        $endpoints[BlueskyClient::REFRESH_SESSION_ENDPOINT] ??= [];
        $endpoints[BlueskyClient::CREATE_RECORD_ENDPOINT] ??= [];
        $endpoints[BlueskyClient::RESOLVE_HANDLE_ENDPOINT] ??= [];
        $endpoints[BlueskyClient::UPLOAD_BLOB_ENDPOINT] ??= [];

        foreach ($endpoints as $endpoint => $data) {
            if (is_numeric($endpoint)) {
                $endpoint = $data;
            }

            $status = Arr::pull($data, key: ':status', default: 200);
            $data = Http::response(
                body: match ($endpoint) {
                    BlueskyClient::CREATE_SESSION_ENDPOINT => self::fakeCreateSessionResponse($data ?? []),
                    BlueskyClient::CREATE_RECORD_ENDPOINT => self::fakeCreatePostResponse($data ?? []),
                    BlueskyClient::REFRESH_SESSION_ENDPOINT => self::fakeRefreshSessionResponse($data ?? []),
                    BlueskyClient::RESOLVE_HANDLE_ENDPOINT => self::fakeResolveHandleResponse($data ?? []),
                    BlueskyClient::UPLOAD_BLOB_ENDPOINT => self::fakeUploadBlobResponse($data ?? []),
                    default => $data
                },
                status: $status,
            );

            Http::fake([
                '*' . explode('?', $endpoint)[0] . '*' => $data,
            ]);
        }
    }

    public static function createIdentity(array $data = []): BlueskyIdentity
    {
        return new BlueskyIdentity(...[
            ...Arr::except(self::$fakeCreateSessionResponse, 'emailConfirmed'),
            ...$data,
        ]);
    }

    public static function fakeResolveHandleResponse(array $data = []): array
    {
        return [
            ...self::$fakeResolveHandleResponse,
            ...$data,
        ];
    }

    public static function fakeCreatePostResponse(array $data = []): array
    {
        return [
            ...self::$fakeCreatePostResponse,
            ...$data,
        ];
    }

    public static function fakeCreateSessionResponse(array $data = []): array
    {
        return [
            ...self::$fakeCreateSessionResponse,
            ...$data,
        ];
    }

    public static function fakeRefreshSessionResponse(array $data = []): array
    {
        return [
            ...self::$fakeRefreshSessionResponse,
            ...$data,
        ];
    }

    public static function fakeUploadBlobResponse(array $data = []): array
    {
        return [
            ...self::$fakeUploadBlobResponse,
            ...$data,
        ];
    }
}
