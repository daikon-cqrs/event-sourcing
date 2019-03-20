<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing\Aggregate\Mock;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\Event\DomainEventInterface;
use Daikon\EventSourcing\Aggregate\Event\DomainEventTrait;

/**
 * @codeCoverageIgnore
 * @aggregateId pizzaId
 */
final class PizzaWasBaked implements DomainEventInterface
{
    use DomainEventTrait;

    /** @var PizzaId */
    private $pizzaId;

    /** @var string[] */
    private $ingredients;

    public static function withIngredients(BakePizza $bakePizza): self
    {
        $pizzaBaked = new static($bakePizza->getPizzaId());
        $pizzaBaked->ingredients = $bakePizza->getIngredients();
        return $pizzaBaked;
    }

    public function conflictsWith(DomainEventInterface $otherEvent): bool
    {
        return false;
    }

    public function getPizzaId(): PizzaId
    {
        return $this->pizzaId;
    }

    /** @return string[] */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    /** @param array $state */
    public static function fromNative($state): self
    {
        $pizzaWasBaked = new self(
            PizzaId::fromNative($state['pizzaId']),
            AggregateRevision::fromNative($state['aggregateRevision'])
        );
        $pizzaWasBaked->ingredients = $state['ingredients'];
        return $pizzaWasBaked;
    }

    public function toNative(): array
    {
        return [
            'pizzaId' => (string)$this->pizzaId,
            'aggregateRevision' => $this->aggregateRevision->toNative(),
            'ingredients' => $this->ingredients
        ];
    }

    protected function __construct(PizzaId $pizzaId, AggregateRevision $aggregateRevision = null)
    {
        $this->pizzaId = $pizzaId;
        $this->aggregateRevision = $aggregateRevision ?? AggregateRevision::makeEmpty();
    }
}
