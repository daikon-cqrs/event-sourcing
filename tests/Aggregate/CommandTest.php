<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing;

use Daikon\Tests\EventSourcing\Aggregate\Mock\BakePizza;
use PHPUnit\Framework\TestCase;

final class CommandTest extends TestCase
{
    public function testFromNative()
    {
        /** @var $bakePizza BakePizza */
        $bakePizza = BakePizza::fromNative([
            'aggregateId' => 'pizza-42-6-23',
            'ingredients' => [ 'mushrooms', 'tomatoes', 'onions' ]
        ]);
        $this->assertEquals('pizza-42-6-23', $bakePizza->getAggregateId());
        $this->assertEquals(0, $bakePizza->getKnownAggregateRevision()->toNative());
        $this->assertEquals([ 'mushrooms', 'tomatoes', 'onions' ], $bakePizza->getIngredients());
    }

    public function testToNative()
    {
        $bakePizzaArray = [
            'aggregateId' => 'pizza-42-6-23',
            'knownAggregateRevision' => 0,
            'ingredients' => [ 'mushrooms', 'tomatoes', 'onions' ]
        ];
        $this->assertEquals($bakePizzaArray, BakePizza::fromNative($bakePizzaArray)->toNative());
    }
}
