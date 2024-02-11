<?php

namespace NotificationChannels\Bluesky\RichText;

use ReflectionClass;
use ReflectionProperty;

trait SerializesIntoPost
{
    public function toArray(): array
    {
        return [
            '$type' => $this->getType(),
            ...$this->serializeProperties(),
        ];
    }

    /** Represents the Bluesky type. */
    abstract public function getType(): string;

    protected function serializeProperties(): array
    {
        return collect((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC))
            ->filter(function (ReflectionProperty $property) {
                if (\count($property->getAttributes(IgnoreProperty::class))) {
                    return false;
                }

                return true;
            })
            ->flatMap(fn (ReflectionProperty $property) => [
                $property->getName() => $this->{$property->getName()},
            ])
            ->map(function (mixed $value) {
                if (\is_object($value) && method_exists($value, 'toArray')) {
                    return $value->toArray();
                }

                return $value;
            })
            ->filter()
            ->toArray();
    }
}
