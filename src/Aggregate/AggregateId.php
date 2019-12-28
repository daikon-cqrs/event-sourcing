<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate;

final class AggregateId implements AggregateIdInterface
{
    use AggregateIdTrait;

    /** @param string $id */
    public static function fromNative($id): AggregateIdInterface
    {
        return new self($id);
    }
}
