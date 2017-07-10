<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing;

use Daikon\EventSourcing\EventStore\CommitInterface;
use Daikon\EventSourcing\EventStore\CommitSequence;
use Daikon\EventSourcing\EventStore\UnitOfWorkInterface;
use Daikon\MessageBus\EnvelopeInterface;
use Daikon\MessageBus\MessageBusInterface;
use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Tests\EventSourcing\Aggregate\Mock\BakePizza;
use Daikon\Tests\EventSourcing\Aggregate\Mock\BakePizzaHandler;
use Daikon\Tests\EventSourcing\Aggregate\Mock\Pizza;
use PHPUnit\Framework\TestCase;

final class CommandHandlerTest extends TestCase
{
    public function testHandleNewAggregate()
    {
        $aggregateId = 'pizza-42-6-23';
        $ingredients = [ 'mushrooms', 'tomatoes', 'onions' ];
        $bakePizzaCommand = BakePizza::fromArray([
            'aggregateId' => $aggregateId,
            'ingredients' => $ingredients
        ]);

        $commitMock = $this->createMock(CommitInterface::class);

        $unitOfWorkMock = $this->getMockBuilder(UnitOfWorkInterface::class)
            ->setMethods([ 'commit', 'checkout' ])->getMock();
        $unitOfWorkMock
            ->expects($this->once())
            ->method('commit')
            ->with($this->callback(
                function (Pizza $pizza) use ($aggregateId, $ingredients) {
                    $this->assertEquals(1, $pizza->getRevision()->toNative());
                    $this->assertEquals($aggregateId, $pizza->getIdentifier()->toNative());
                    $this->assertEquals($ingredients, $pizza->getIngredients());
                    return true;
                }
            ))
            ->willReturn(new CommitSequence([ $commitMock ]));

        $messageBusMock = $this->getMockBuilder(MessageBusInterface::class)
            ->setMethods(['publish', 'receive'])->getMock();
        $messageBusMock
            ->expects($this->once())
            ->method('publish')
            ->with($this->callback(
                function (CommitInterface $commit) use ($commitMock) {
                    $this->assertEquals($commit, $commitMock);
                    return true;
                }
            ))
            ->willReturn(true);

        $envelopeMock = $this->createMock(EnvelopeInterface::class);
        $envelopeMock
            ->expects($this->once())
            ->method('getMessage')
            ->willReturn($bakePizzaCommand);
        $envelopeMock
            ->expects($this->once())
            ->method('getMetadata')
            ->willReturn(Metadata::makeEmpty());

        (new BakePizzaHandler($unitOfWorkMock, $messageBusMock))->handle($envelopeMock);
    }
}
