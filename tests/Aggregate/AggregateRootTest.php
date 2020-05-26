<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Tests\EventSourcing;

use ArrayIterator;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\Tests\EventSourcing\Aggregate\Mock\BakePizza;
use Daikon\Tests\EventSourcing\Aggregate\Mock\Pizza;
use Daikon\Tests\EventSourcing\Aggregate\Mock\PizzaId;
use Daikon\Tests\EventSourcing\Aggregate\Mock\PizzaWasBaked;
use Exception;
use PHPUnit\Framework\TestCase;

final class AggregateRootTest extends TestCase
{
    public function testStartAggregateRootLifecycle(): void
    {
        $ingredients = ['mushrooms', 'tomatoes', 'onions'];
        /** @var BakePizza $bakePizza */
        $bakePizza = BakePizza::fromNative([
            'pizzaId' => 'pizza-42-6-23',
            'ingredients' => $ingredients
        ]);
        $pizza = Pizza::bake(PizzaWasBaked::fromCommand($bakePizza));

        $this->assertEquals('pizza-42-6-23', $pizza->getIdentifier());
        $this->assertEquals(1, $pizza->getRevision()->toNative());
        $this->assertEquals($ingredients, $pizza->getIngredients()->toNative());
        $this->assertCount(1, $pizza->getTrackedEvents());
    }

    public function testReconstituteFromHistory(): void
    {
        $pizzaId = PizzaId::fromNative('pizza-42-6-23');
        $ingredients = ['mushrooms', 'tomatoes', 'onions'];
        $pizzaWasBaked = PizzaWasBaked::fromNative([
            'pizzaId' => (string)$pizzaId,
            'revision' => 1,
            'ingredients' => $ingredients
        ]);

        $domainEventSequenceMock = $this->createMock(DomainEventSequenceInterface::class);
        $domainEventSequenceMock
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new ArrayIterator([$pizzaWasBaked]));

        $pizza = Pizza::reconstituteFromHistory($pizzaId, $domainEventSequenceMock);

        $this->assertEquals($pizzaId, $pizza->getIdentifier());
        $this->assertEquals(1, $pizza->getRevision()->toNative());
        $this->assertEquals($ingredients, $pizza->getIngredients()->toNative());
        $this->assertCount(0, $pizza->getTrackedEvents());
    }

    public function testReconstituteWithUnexpectedRevision(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Given event revision 2 does not match expected AR revision at 1');

        $pizzaId = PizzaId::fromNative('pizza-42-6-23');
        $pizzaWasBaked = PizzaWasBaked::fromNative([
            'pizzaId' => (string)$pizzaId,
            'revision' => 2, // unexpected revision will trigger an error
            'ingredients' => []
        ]);

        $domainEventSequenceMock = $this->createMock(DomainEventSequenceInterface::class);
        $domainEventSequenceMock
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new ArrayIterator([$pizzaWasBaked]));

        Pizza::reconstituteFromHistory($pizzaId, $domainEventSequenceMock);
    } // @codeCoverageIgnore

    public function testReconstituteWithUnexpectedId(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Given event identifier pizza-23-22-5 does not match expected AR identifier at pizza-42-6-23'
        );

        $pizzaId = PizzaId::fromNative('pizza-42-6-23');
        $pizzaWasBaked = PizzaWasBaked::fromNative([
            'pizzaId' => 'pizza-23-22-5', // unexpected id will trigger an error
            'revision' => 1,
            'ingredients' => []
        ]);

        $domainEventSequenceMock = $this->createMock(DomainEventSequenceInterface::class);
        $domainEventSequenceMock
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new ArrayIterator([$pizzaWasBaked]));

        Pizza::reconstituteFromHistory($pizzaId, $domainEventSequenceMock);
    } // @codeCoverageIgnore
}
