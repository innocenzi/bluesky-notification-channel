<?php

namespace NotificationChannels\Bluesky\RichText\Facets;

use NotificationChannels\Bluesky\RichText\IgnoreProperty;
use ReflectionClass;
use ReflectionProperty;

abstract class FacetFeature
{
    public function toArray(): array
    {
        $properties = collect((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC))
            ->filter(function (ReflectionProperty $property) {
                if (\count($property->getAttributes(IgnoreProperty::class))) {
                    return false;
                }

                return true;
            })
            ->flatMap(fn (ReflectionProperty $property) => [
                $property->getName() => $this->{$property->getName()},
            ])
            ->filter()
            ->toArray();

        return [
            '$type' => $this->getType(),
            ...$properties,
        ];
    }

    /** Represents the Bluesky type. */
    abstract protected function getType(): string;
}
