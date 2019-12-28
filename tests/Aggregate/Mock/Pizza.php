<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Tests\EventSourcing\Aggregate\Mock;

use Daikon\EventSourcing\Aggregate\AggregateRootInterface;
use Daikon\EventSourcing\Aggregate\AggregateRootTrait;

/**
 * @codeCoverageIgnore
 */
final class Pizza implements AggregateRootInterface
{
    use AggregateRootTrait;

    /** @var string[] */
    private $ingredients = [];

    public static function bake(BakePizza $bakePizza): self
    {
        $pizza = new static($bakePizza->getAggregateId());
        $pizza = $pizza->reflectThat(PizzaWasBaked::withIngredients($bakePizza));
        return $pizza;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    protected function whenPizzaWasBaked(PizzaWasBaked $pizzaWasBaked): void
    {
        $this->ingredients = $pizzaWasBaked->getIngredients();
    }
}
