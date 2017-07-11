<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing\Aggregate\Mock;

use Daikon\EventSourcing\Aggregate\AggregateAlias;
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

    public static function getAlias(): AggregateAlias
    {
        static $alias;
        $alias = AggregateAlias::fromNative('testing.mock.pizza');
        return $alias;
    }

    public static function bake(BakePizza $bakePizza): self
    {
        $pizza = new static($bakePizza->getAggregateId());
        $pizza = $pizza->reflectThat(PizzaWasBaked::withIngredients($bakePizza));
        return $pizza;
    }

    /**
     * @return string[]
     */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    protected function whenPizzaWasBaked(PizzaWasBaked $pizzaWasBaked): void
    {
        $this->ingredients = $pizzaWasBaked->getIngredients();
    }
}
