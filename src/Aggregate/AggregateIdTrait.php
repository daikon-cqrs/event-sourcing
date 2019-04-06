<?php
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

use Assert\Assertion;

trait AggregateIdTrait
{
    /** @var string */
    private $id;

    /** @param string $id */
    public static function fromNative($id): AggregateIdInterface
    {
        return new static($id);
    }

    public function toNative(): string
    {
        return $this->id;
    }

    /** @param static $comparator */
    public function equals($comparator): bool
    {
        Assertion::isInstanceOf($comparator, static::class);
        return $this->id === $comparator->toNative();
    }

    public function __toString(): string
    {
        return $this->id;
    }

    private function __construct(string $id)
    {
        Assertion::regex($id, static::PATTERN);
        $this->id = $id;
    }
}
