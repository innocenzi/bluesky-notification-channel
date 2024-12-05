<?php

namespace NotificationChannels\Bluesky;

use NotificationChannels\Bluesky\Embeds\Embed;
use NotificationChannels\Bluesky\Embeds\EmbedResolver;
use NotificationChannels\Bluesky\Facets\FacetsResolver;
use NotificationChannels\Bluesky\IdentityRepository\IdentityRepository;

final class BlueskyService
{
    public function __construct(
        protected readonly BlueskyClient $client,
        protected readonly IdentityRepository $identityRepository,
        protected readonly SessionManager $sessionManager,
        protected readonly EmbedResolver $embedResolver,
        protected readonly FacetsResolver $facetsResolver,
    ) {
    }

    public function createPost(BlueskyPost|string $post): string
    {
        return $this->client->createPost(
            identity: $this->sessionManager->getIdentity(),
            post: $this->resolvePost($post),
        );
    }

    public function uploadBlob(string $pathOrUrl): Blob
    {
        return $this->client->uploadBlob(
            identity: $this->sessionManager->getIdentity(),
            pathOrUrl: $pathOrUrl,
        );
    }

    public function resolvePost(string|BlueskyPost $post): BlueskyPost
    {
        if (\is_string($post)) {
            $post = BlueskyPost::make()->text($post);
        }

        if ($post->automaticallyResolvesFacets()) {
            $post->facets(facets: $this->facetsResolver->resolve($this, $post));
        }

        if ($embed = $this->resolveEmbed($post)) {
            $post->embed(embed: $embed);
        }

        return $post;
    }

    private function resolveEmbed(BlueskyPost $post): ?Embed
    {
        if ($post->embedUrl) {
            return $this->embedResolver->createEmbedFromUrl($this, $post->embedUrl);
        }

        if ($post->automaticallyResolvesEmbeds()) {
            return $this->embedResolver->resolve($this, $post);
        }

        return null;
    }

    public function getClient(): BlueskyClient
    {
        return $this->client;
    }
}
