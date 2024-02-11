<?php

namespace NotificationChannels\Bluesky\Embeds;

use NotificationChannels\Bluesky\RichText\SerializesIntoPost;

abstract class Embed
{
    use SerializesIntoPost;
}
