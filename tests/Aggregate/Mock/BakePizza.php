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
use Daikon\EventSourcing\Aggregate\Command\CommandInterface;
use Daikon\EventSourcing\Aggregate\Command\CommandTrait;
use Daikon\Interop\FromToNativeTrait;

/**
 * @codeCoverageIgnore
 * @id(pizzaId)
 * @rev(revision)
 */
final class BakePizza implements CommandInterface
{
    use FromToNativeTrait;
    use CommandTrait;

    /** @var PizzaId */
    private $pizzaId;

    /** @var AggregateRevision */
    private $revision;

    /** @var string[] */
    private $ingredients;

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
        $bakePizza = new self(PizzaId::fromNative($state['pizzaId']));
        $bakePizza->ingredients = $state['ingredients'];
        return $bakePizza;
    }

    public function toNative(): array
    {
        return [
            'pizzaId' => (string)$this->pizzaId,
            'revision' => $this->revision->toNative(),
            'ingredients' => $this->ingredients
        ];
    }

    protected function __construct(PizzaId $pizzaId, AggregateRevision $knownRevision = null)
    {
        $this->pizzaId = $pizzaId;
        $this->revision = $knownRevision ?? AggregateRevision::makeEmpty();
    }
}
