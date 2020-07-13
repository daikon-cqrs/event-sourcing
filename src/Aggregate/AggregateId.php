<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate;

use Daikon\Interop\Assertion;

class AggregateId implements AggregateIdInterface
{
    public const PATTERN = '/^\S$|\S.*\S$/';

    private string $identifier;

    /**
     * @param string $identifier
     * @return static
     */
    public static function fromNative($identifier): self
    {
        Assertion::regex($identifier, static::PATTERN, 'Invalid id format.');
        return new static($identifier);
    }

    public function toNative(): string
    {
        return $this->identifier;
    }

    /** @param static $comparator */
    public function equals($comparator): bool
    {
        Assertion::isInstanceOf($comparator, static::class);
        return $this->toNative() === $comparator->toNative();
    }

    public function __toString(): string
    {
        return $this->identifier;
    }

    private function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }
}
