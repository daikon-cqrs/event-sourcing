<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate;

use Assert\Assertion;

trait AggregateIdTrait
{
    private string $id;

    /** @param string $id */
    public static function fromNative($id): self
    {
        Assertion::regex($id, static::PATTERN);
        return new static($id);
    }

    public function toNative(): string
    {
        return $this->id;
    }

    /** @param self $comparator */
    public function equals($comparator): bool
    {
        Assertion::isInstanceOf($comparator, static::class);
        return $this->toNative() === $comparator->toNative();
    }

    public function __toString(): string
    {
        return $this->id;
    }

    private function __construct(string $id)
    {
        $this->id = $id;
    }
}
