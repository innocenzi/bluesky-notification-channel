<?php

namespace NotificationChannels\Bluesky;

class BlueskyPost
{
    public function __construct(
        public string $text = '',
    ) {
    }

    public function __toString(): string
    {
        return $this->text;
    }

    public static function make(): static
    {
        return new static();
    }

    /**
     * Sets the post's text.
     */
    public function text(?string $text): static
    {
        $this->text = $text ?? '';

        return $this;
    }
}
