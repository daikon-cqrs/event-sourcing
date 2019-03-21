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
 * @id(pizzaId)
 * @rev(revision)
 */
final class PizzaWasBaked implements DomainEventInterface
{
    use DomainEventTrait;

    /** @var PizzaId */
    private $pizzaId;

    /** @var AggregateRevision */
    private $revision;

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

    public function getRevision(): AggregateRevision
    {
        return $this->revision;
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
            AggregateRevision::fromNative($state['revision'])
        );
        $pizzaWasBaked->ingredients = $state['ingredients'];
        return $pizzaWasBaked;
    }

    public function toNative(): array
    {
        return [
            'pizzaId' => (string)$this->pizzaId,
            'revision' => $this->revision->toNative(),
            'ingredients' => $this->ingredients
        ];
    }

    protected function __construct(PizzaId $pizzaId, AggregateRevision $revision = null)
    {
        $this->pizzaId = $pizzaId;
        $this->revision = $revision ?? AggregateRevision::makeEmpty();
    }
}
