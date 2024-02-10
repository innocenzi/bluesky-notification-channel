<h2 align="center">Bluesky notification channel</h2>

<p align="center">
  <a href="https://github.com/innocenzi/bluesky-notification-channel/actions?query=workflow%3Arun-tests">
    <img alt="Status" src="https://github.com/innocenzi/bluesky-notification-channel/actions/workflows/run-tests.yml/badge.svg">
  </a>
  <span>&nbsp;</span>
  <a href="https://packagist.org/packages/innocenzi/bluesky-notification-channel">
    <img alt="npm" src="https://img.shields.io/packagist/v/innocenzi/bluesky-notification-channel">
  </a>
  <br />
  <br />
  <p align="center">
    Notification channel implementation to create Bluesky posts using Laravel.
  </p>
  <pre><div align="center">composer require innocenzi/bluesky-notification-channel</div></pre>
</p>

&nbsp;

## Configuring credentials

To interact with its API, Bluesky recommends creating an [application-specific password](https://atproto.com/specs/xrpc#app-passwords) instead of using your account's main password. You may generate one in your [account settings](https://bsky.app/settings/app-passwords). Once created, fill your `.env` accordingly:

```env
BLUESKY_USERNAME=your-handle
BLUESKY_PASSWORD=your-app-password
```

Add these values to `config/services.php`:

```php
return [
    // ...
    'bluesky' => [
      'username' => env('BLUESKY_USERNAME'),
      'password' => env('BLUESKY_PASSWORD'),
    ]
];
```


&nbsp;

## Publishing posts

To create a post, you will need to instruct the [notification](https://laravel.com/docs/master/notifications#generating-notifications) of your choice to use the `BlueskyChannel` channel and to implement the corresponding `toBluesky` method. 

This method may return a `BlueskyPost` instance or a simple `string`.

```php
final class CreateBlueskyPost extends Notification
{
    public function via(object $notifiable): array
    {
        return [
            BlueskyChannel::class
        ];
    }

    public function toBluesky(object $notifiable): BlueskyPost
    {
        return BlueskyPost::make()
            ->text('Test from Laravel');
    }
}
```


&nbsp;

## Sessions

Bluesky doesn't provide a way to authenticate requests using classic API tokens. Instead, they only offer a JWT-based authentication system, including an access and a refresh token.

Since these tokens expire, they cannot be stored in the environment. They are generated dynamically by creating and refreshing sessions and they need to be kept for as long as possible.

This notification channel implementation uses a session manager and an identity repository based on Laravel's cache. This may be overriden by replacing the binding in the container.

Additionnally, the key used by the cache-based identity repository may be configured by setting the `services.bluesky.identity_cache_key` option.
