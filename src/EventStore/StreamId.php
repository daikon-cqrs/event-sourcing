<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore;

use Assert\Assertion;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class StreamId implements ValueObjectInterface
{
    /** @var string */
    private $id;

    public static function fromNative($id): ValueObjectInterface
    {
        return new self(trim($id));
    }

    public static function makeEmpty(): ValueObjectInterface
    {
        throw new \Exception("Creating empty stream-ids is not supported.");
    }

    public function toNative()
    {
        return $this->id;
    }

    public function equals(ValueObjectInterface $streamId): bool
    {
        Assertion::isInstanceOf($streamId, static::class);
        return $this->id === $streamId->toNative();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    private function __construct(string $id)
    {
        Assertion::notEmpty($id);
        $this->id = $id;
    }
}
