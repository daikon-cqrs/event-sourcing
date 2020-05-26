<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Tests\EventSourcing\Aggregate\Mock;

use Daikon\EventSourcing\Aggregate\AggregateRootInterface;
use Daikon\EventSourcing\Aggregate\AggregateRootTrait;
use Daikon\ValueObject\TextList;

/**
 * @codeCoverageIgnore
 */
final class Pizza implements AggregateRootInterface
{
    use AggregateRootTrait;

    private TextList $ingredients;

    public static function bake(PizzaWasBaked $pizzaWasBaked): self
    {
        return (new self($pizzaWasBaked->getAggregateId()))
            ->reflectThat($pizzaWasBaked);
    }

    public function getIngredients(): TextList
    {
        return $this->ingredients;
    }

    protected function whenPizzaWasBaked(PizzaWasBaked $pizzaWasBaked): void
    {
        $this->ingredients = $pizzaWasBaked->getIngredients();
    }
}
