<?php

namespace NotificationChannels\Bluesky\Embeds;

use NotificationChannels\Bluesky\BlueskyPost;
use NotificationChannels\Bluesky\BlueskyService;

interface EmbedResolver
{
    /**
     * Resolves an embed from the given post.
     */
    public function resolve(BlueskyService $bluesky, BlueskyPost $post): ?Embed;
}
