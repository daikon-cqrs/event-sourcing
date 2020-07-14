<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Tests\EventSourcing\Aggregate\Mock;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\Interop\FromToNativeTrait;
use Daikon\ValueObject\TextList;

/**
 * @codeCoverageIgnore
 * @id(pizzaId, Daikon\Tests\EventSourcing\Aggregate\Mock\PizzaId)
 * @rev(revision, Daikon\EventSourcing\Aggregate\AggregateRevision)
 * @map(ingredients, Daikon\ValueObject\TextList::fromNative)
 */
trait BakeMessageTrait
{
    use FromToNativeTrait;

    private PizzaId $pizzaId;

    private AggregateRevision $revision;

    private TextList $ingredients;

    public function getPizzaId(): PizzaId
    {
        return $this->pizzaId;
    }

    public function getRevision(): AggregateRevision
    {
        return $this->revision;
    }

    public function getIngredients(): TextList
    {
        return $this->ingredients;
    }
}
