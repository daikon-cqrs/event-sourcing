<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing;

use Daikon\EventSourcing\EventStore\Commit;
use Daikon\EventSourcing\EventStore\CommitInterface;
use Daikon\EventSourcing\EventStore\CommitSequence;
use Daikon\EventSourcing\EventStore\UnitOfWorkInterface;
use Daikon\MessageBus\Envelope;
use Daikon\MessageBus\MessageBusInterface;
use Daikon\Tests\EventSourcing\Aggregate\Mock\BakePizza;
use Daikon\Tests\EventSourcing\Aggregate\Mock\BakePizzaHandler;
use Daikon\Tests\EventSourcing\Aggregate\Mock\Pizza;
use Daikon\Tests\EventSourcing\Aggregate\Mock\PizzaWasBaked;
use PHPUnit\Framework\TestCase;

final class CommandHandlerTest extends TestCase
{
    public function testHandleNewAggregate()
    {
        $commitExpectation = [
            '@type' => Commit::class,
            'streamId' => 'pizza-42-6-23',
            'streamRevision' => 1,
            'metadata' => [],
            'eventLog' => [[
                '@type' => PizzaWasBaked::class,
                'aggregateId' => 'pizza-42-6-23',
                'aggregateRevision' => 1,
                'ingredients' => [ 'mushrooms', 'tomatoes', 'onions' ]
            ]]
        ];

        $unitOfWorkMock = $this->getMockBuilder(UnitOfWorkInterface::class)
            ->setMethods([ 'commit', 'checkout' ])->getMock();
        $unitOfWorkMock
            ->expects($this->once())
            ->method('commit')
            ->with($this->callback(
                function (Pizza $pizza) {
                    $this->assertEquals(1, $pizza->getRevision()->toNative());
                    $this->assertEquals('pizza-42-6-23', $pizza->getIdentifier()->toNative());
                    $this->assertEquals([ 'mushrooms', 'tomatoes', 'onions' ], $pizza->getIngredients());
                    return true;
                }
            ))
            ->willReturn(CommitSequence::fromArray([ $commitExpectation ]));

        $messageBusMock = $this->getMockBuilder(MessageBusInterface::class)
            ->setMethods(['publish', 'receive'])->getMock();
        $messageBusMock
            ->expects($this->once())
            ->method('publish')
            ->with($this->callback(
                function (CommitInterface $commit) use ($commitExpectation) {
                    $this->assertEquals($commitExpectation, $commit->toArray());
                    return true;
                }
            ))
            ->willReturn(true);

        (new BakePizzaHandler($unitOfWorkMock, $messageBusMock))
            ->handle(Envelope::wrap(BakePizza::fromArray([
                'aggregateId' => 'pizza-42-6-23',
                'ingredients' => [ 'mushrooms', 'tomatoes', 'onions' ]
            ])));
    }
}
