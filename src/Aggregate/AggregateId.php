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

final class AggregateId implements AggregateIdInterface
{
    /** @var string */
    private $id;

    /** @param string $id */
    public static function fromNative($id): AggregateIdInterface
    {
        return new self(trim($id));
    }

    public function toNative(): string
    {
        return $this->id;
    }

    public function equals(AggregateIdInterface $streamId): bool
    {
        Assertion::isInstanceOf($streamId, self::class);
        return $this->id === $streamId->toNative();
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
