<?php

namespace NotificationChannels\Bluesky\RichText;

use Attribute;

/**
 * Marker attribute to ignore the property when serializing to a payload.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class IgnoreProperty
{
}
