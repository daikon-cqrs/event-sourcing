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
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\Tests\EventSourcing\Aggregate\Mock\BakePizza;
use Daikon\Tests\EventSourcing\Aggregate\Mock\Pizza;
use Daikon\Tests\EventSourcing\Aggregate\Mock\PizzaWasBaked;
use PHPUnit\Framework\TestCase;

final class AggregateRootTest extends TestCase
{
    public function testStartAggregateRootLifecycle()
    {
        $ingredients = ['mushrooms', 'tomatoes', 'onions'];
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
        $ingredients = ['mushrooms', 'tomatoes', 'onions'];
        $pizzaWasBaked = PizzaWasBaked::fromArray([
            'aggregateId' => (string)$pizzaId,
            'aggregateRevision' => 1,
            'ingredients' => $ingredients
        ]);

        $domainEventSequenceMock = $this->createMock(DomainEventSequenceInterface::class);
        $domainEventSequenceMock
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([ $pizzaWasBaked ]));

        /** @var $pizza Pizza */
        $pizza = Pizza::reconstituteFromHistory($pizzaId, $domainEventSequenceMock);

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
        $pizzaWasBaked = PizzaWasBaked::fromArray([
            'aggregateId' => (string)$pizzaId,
            'aggregateRevision' => 2, // unexpected revision will trigger an error
            'ingredients' => []
        ]);

        $domainEventSequenceMock = $this->createMock(DomainEventSequenceInterface::class);
        $domainEventSequenceMock
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([ $pizzaWasBaked ]));

        Pizza::reconstituteFromHistory($pizzaId, $domainEventSequenceMock);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Given event-identifier pizza-23-22-5
     *                           does not match expected AR identifier at pizza-42-6-23
     */
    public function testReconstituteWithUnexpectedId()
    {
        /** @var $pizzaId AggregateId */
        $pizzaId = AggregateId::fromNative('pizza-42-6-23');
        $pizzaWasBaked = PizzaWasBaked::fromArray([
            'aggregateId' => 'pizza-23-22-5', // unexpected id will trigger an error
            'aggregateRevision' => 1,
            'ingredients' => []
        ]);

        $domainEventSequenceMock = $this->createMock(DomainEventSequenceInterface::class);
        $domainEventSequenceMock
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([ $pizzaWasBaked ]));

        Pizza::reconstituteFromHistory($pizzaId, $domainEventSequenceMock);
    } // @codeCoverageIgnore
}
