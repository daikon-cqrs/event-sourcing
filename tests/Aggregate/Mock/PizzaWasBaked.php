<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing\Aggregate\Mock;

use Daikon\EventSourcing\Aggregate\AggregateId;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\DomainEvent;
use Daikon\EventSourcing\Aggregate\DomainEventInterface;
use Daikon\MessageBus\MessageInterface;

/**
 * @codeCoverageIgnore
 */
final class PizzaWasBaked extends DomainEvent
{
    /** @var string[] */
    private $ingredients;

    public static function withIngredients(BakePizza $bakePizza): self
    {
        $pizzaBaked = new static($bakePizza->getAggregateId());
        $pizzaBaked->ingredients = $bakePizza->getIngredients();
        return $pizzaBaked;
    }

    public static function getAggregateRootClass(): string
    {
        return Pizza::class;
    }

    public function conflictsWith(DomainEventInterface $otherEvent): bool
    {
        return false;
    }

    public static function fromArray(array $data): MessageInterface
    {
        $pizzaWasBaked = new static(
            AggregateId::fromNative($data['aggregateId']),
            AggregateRevision::fromNative($data['aggregateRevision'])
        );
        $pizzaWasBaked->ingredients = $data['ingredients'];
        return $pizzaWasBaked;
    }

    /**
     * @return string[]
     */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['ingredients'] = $this->ingredients;
        return $data;
    }
}
