<?php

namespace Daikon\Cqrs\EventStore;

use Daikon\Entity\ValueObject\ValueObjectInterface;

final class CommitStreamId implements ValueObjectInterface
{
    private $identifier;

    public static function fromNative($identifier): ValueObjectInterface
    {
        return new self($identifier);
    }

    public static function makeEmpty(): ValueObjectInterface
    {
        return new static(null);
    }

    public function toNative()
    {
        return $this->identifier;
    }

    public function equals(ValueObjectInterface $streamId): bool
    {
        return $this->identifier === $streamId->toNative();
    }

    public function isEmpty(): bool
    {
        return empty($this->identifier);
    }

    public function __toString(): string
    {
        return $this->identifier ?? '';
    }

    private function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }
}
