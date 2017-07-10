<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing;

use Daikon\EventSourcing\Aggregate\AggregateId;
use Daikon\EventSourcing\Aggregate\DomainEventSequence;
use Daikon\Tests\EventSourcing\Aggregate\Mock\BakePizza;
use Daikon\Tests\EventSourcing\Aggregate\Mock\Pizza;
use Daikon\Tests\EventSourcing\Aggregate\Mock\PizzaWasBaked;
use PHPUnit\Framework\TestCase;

final class AggregateRootTest extends TestCase
{
    public function testStartAggregateRootLifecycle()
    {
        $ingredients = [ 'mushrooms', 'tomatoes', 'onions' ];
        /** @var $bakePizza BakePizza */
        $bakePizza = BakePizza::fromArray([
            'aggregateId' => 'pizza-42-6-23',
            'ingredients' => $ingredients
        ]);
        $pizza = Pizza::bake($bakePizza);

        $this->assertEquals('pizza-42-6-23', $pizza->getIdentifier());
        $this->assertEquals(1, $pizza->getRevision()->toNative());
        $this->assertEquals($ingredients, $pizza->getIngredients());
        $this->assertCount(1, $pizza->getTrackedEvents());
    }

    public function testReconstituteFromHistory()
    {
        /** @var $pizzaId AggregateId */
        $pizzaId = AggregateId::fromNative('pizza-42-6-23');
        $ingredients = [ 'mushrooms', 'tomatoes', 'onions' ];
        $history = new DomainEventSequence([
            PizzaWasBaked::fromArray([
                'aggregateId' => (string)$pizzaId,
                'aggregateRevision' => 1,
                'ingredients' => $ingredients
            ])
        ]);
        ;
        /** @var $pizza Pizza */
        $pizza = Pizza::reconstituteFromHistory($pizzaId, $history);

        $this->assertEquals($pizzaId, $pizza->getIdentifier());
        $this->assertEquals(1, $pizza->getRevision()->toNative());
        $this->assertEquals($ingredients, $pizza->getIngredients());
        $this->assertCount(0, $pizza->getTrackedEvents());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Given event-revision 2 does not match expected AR revision at 1
     */
    public function testReconstituteWithUnexpectedRevision()
    {
        /** @var $pizzaId AggregateId */
        $pizzaId = AggregateId::fromNative('pizza-42-6-23');
        Pizza::reconstituteFromHistory($pizzaId, new DomainEventSequence([
            PizzaWasBaked::fromArray([
                'aggregateId' => (string)$pizzaId,
                'aggregateRevision' => 2, // unexpected revision
                'ingredients' => [ 'mushrooms', 'tomatoes', 'onions' ]
            ])
        ]));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Given event-identifier pizza-23-22-5
                                 does not match expected AR identifier at pizza-42-6-23
     */
    public function testReconstituteWithUnexpectedId()
    {
        /** @var $pizzaId AggregateId */
        $pizzaId = AggregateId::fromNative('pizza-42-6-23');
        Pizza::reconstituteFromHistory($pizzaId, new DomainEventSequence([
            PizzaWasBaked::fromArray([
                'aggregateId' => 'pizza-23-22-5', // unexpected id
                'aggregateRevision' => 1,
                'ingredients' => [ 'mushrooms', 'tomatoes', 'onions' ]
            ])
        ]));
    }
}
