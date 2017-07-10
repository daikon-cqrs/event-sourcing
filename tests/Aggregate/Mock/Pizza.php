<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing\Aggregate\Mock;

use Daikon\EventSourcing\Aggregate\AggregateRootInterface;
use Daikon\EventSourcing\Aggregate\AggregateRootTrait;

final class Pizza implements AggregateRootInterface
{
    use AggregateRootTrait;

    private $ingredients;

    public static function bake(BakePizza $bakePizza): self
    {
        $pizza = new static($bakePizza->getAggregateId());
        $pizza = $pizza->reflectThat(
            PizzaWasBaked::withIngredients($bakePizza->getAggregateId(), $bakePizza->getIngredients())
        );
        return $pizza;
    }

    protected function whenPizzaWasBaked(PizzaWasBaked $pizzaWasBaked)
    {
        $this->ingredients = $pizzaWasBaked->getIngredients();
    }
}
