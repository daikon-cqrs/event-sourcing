<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\Aggregate;

use Daikon\Entity\ValueObject\ValueObjectInterface;
use Assert\Assertion;

class AggregateId implements AggregateIdInterface
{
    private $id;

    public static function fromNative($id): ValueObjectInterface
    {
        return new self(trim($id));
    }

    public static function makeEmpty(): ValueObjectInterface
    {
        throw new \Exception("Creating empty aggregate-ids is not supported.");
    }

    public function toNative()
    {
        return $this->id;
    }

    public function equals(ValueObjectInterface $aggregateId): bool
    {
        Assertion::isInstanceOf($aggregateId, static::class);
        return $this->id === $aggregateId->toNative();
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
