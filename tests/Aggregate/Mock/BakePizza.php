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
use Daikon\EventSourcing\Aggregate\Command;
use Daikon\MessageBus\MessageInterface;

final class BakePizza extends Command
{
    private $ingredients;

    public static function getAggregateRootClass(): string
    {
        return Pizza::class;
    }

    public static function fromArray(array $data): MessageInterface
    {
        $bakePizza = new static(AggregateId::fromNative($data['aggregateId']));
        $bakePizza->ingredients = $data['ingredients'];
        return $bakePizza;
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
