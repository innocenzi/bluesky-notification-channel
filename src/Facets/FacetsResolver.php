<?php

namespace NotificationChannels\Bluesky\Facets;

use NotificationChannels\Bluesky\BlueskyPost;
use NotificationChannels\Bluesky\BlueskyService;

interface FacetsResolver
{
    /**
     * Resolves facets from the given post.
     */
    public function resolve(BlueskyService $bluesky, BlueskyPost $post): array;
}
