<?php

use NotificationChannels\Bluesky\BlueskyPost;

test('`BlueskyPost` can be converted to a string', function () {
    $post = BlueskyPost::make()->text('foo');

    expect((string) $post)->toBe('foo');
});

test('`BlueskyPost` has an accessble `text` property', function () {
    $post = BlueskyPost::make()->text('foo');

    expect($post->text)->toBe('foo');
});
