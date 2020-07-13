<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Tests\EventSourcing\Aggregate\Mock;

use Daikon\EventSourcing\Aggregate\Event\AnnotatedEvent;
use Daikon\EventSourcing\Aggregate\Event\DomainEventInterface;

/**
 * @codeCoverageIgnore
 */
final class PizzaWasBaked implements DomainEventInterface
{
    use AnnotatedEvent;
    use BakeMessageTrait;

    public static function fromCommand(BakePizza $bakePizza): self
    {
        return self::fromNative($bakePizza->toNative());
    }

    public function conflictsWith(DomainEventInterface $otherEvent): bool
    {
        return false;
    }
}
