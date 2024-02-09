<h2 align="center">Bluesky notification channel for Laravel</h2>

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
    Create Bluesky posts using Laravel notifications
  </p>
  <pre><div align="center">composer require innocenzi/bluesky-notification-channel</div></pre>
</p>

&nbsp;

## Usage

### Configure your account's credentials

To interact with its API, Bluesky recommends creation an application-specific password instead of using your account's main password.

You may create one in your [account settings](https://bsky.app/settings/app-passwords). Once created, fill your `.env` accordingly:

```env
BLUESKY_USERNAME=your-handle
BLUESKY_PASSWORD=your-app-password
```

Add add these values to `config/services.php`:

```php
return [
    // ...
    'bluesky' => [
      'username' => env('BLUESKY_USERNAME'),
      'password' => env('BLUESKY_PASSWORD'),
  ]
];
```

### Creating posts

To create a post, you will need to create a [new notification](https://laravel.com/docs/master/notifications#generating-notifications) and configure the `BlueskyChannel` channel:

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

The `toBluesky` method can return a `string` or a `BlueskyPost` instance.
