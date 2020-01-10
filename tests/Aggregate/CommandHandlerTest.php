<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Tests\EventSourcing;

use ArrayIterator;
use Daikon\EventSourcing\Aggregate\AggregateRootInterface;
use Daikon\EventSourcing\Aggregate\Command\CommandHandler;
use Daikon\EventSourcing\Aggregate\Command\CommandInterface;
use Daikon\EventSourcing\EventStore\Commit\CommitInterface;
use Daikon\EventSourcing\EventStore\Commit\CommitSequenceInterface;
use Daikon\EventSourcing\EventStore\UnitOfWorkInterface;
use Daikon\MessageBus\Channel\Subscription\MessageHandler\MessageHandlerInterface;
use Daikon\MessageBus\EnvelopeInterface;
use Daikon\MessageBus\MessageBusInterface;
use Daikon\Metadata\Metadata;
use PHPUnit\Framework\TestCase;

final class CommandHandlerTest extends TestCase
{
    public function testHandleNewAggregate(): void
    {
        $commitStub = $this->createMock(CommitInterface::class);
        $commitSequenceStub = $this->createMock(CommitSequenceInterface::class);
        $commitSequenceStub
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new ArrayIterator([$commitStub]));

        $unitOfWorkStub = $this->createMock(UnitOfWorkInterface::class);
        $unitOfWorkStub
            ->expects($this->once())
            ->method('commit')
            ->with($this->isInstanceOf(AggregateRootInterface::class))
            ->willReturn($commitSequenceStub);

        $messageBusStub = $this->createMock(MessageBusInterface::class);
        $messageBusStub
            ->expects($this->once())
            ->method('publish')
            ->with($commitStub, 'commits');

        $envelopeStub = $this->createMock(EnvelopeInterface::class);
        $envelopeStub
            ->expects($this->once())
            ->method('getMetadata')
            ->willReturn(Metadata::makeEmpty());
        $envelopeStub
            ->expects($this->once())
            ->method('getMessage')
            ->willReturn(
                $this->getMockBuilder(CommandInterface::class)->setMockClassName('FooBar')->getMock()
            );

        $commandHandler = $this->getMockBuilder(CommandHandler::class)
            ->setConstructorArgs([$unitOfWorkStub, $messageBusStub])
            ->addMethods(['handleFooBar'])
            ->getMock();
        $commandHandler
            ->expects($this->once())
            ->method('handleFooBar')
            ->willReturn([$this->createMock(AggregateRootInterface::class), Metadata::makeEmpty()]);

        /** @psalm-suppress UndefinedInterfaceMethod */
        $commandHandler->handle($envelopeStub);
    }
}
