<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

use Daikon\EventSourcing\EventStore\CommitInterface;
use Daikon\EventSourcing\EventStore\StreamRevision;
use Daikon\EventSourcing\EventStore\UnitOfWorkInterface;
use Daikon\MessageBus\Channel\Subscription\MessageHandler\MessageHandlerInterface;
use Daikon\MessageBus\EnvelopeInterface;
use Daikon\MessageBus\MessageBusInterface;
use Daikon\MessageBus\Metadata\Metadata;

abstract class CommandHandler implements MessageHandlerInterface
{
    /** @var MessageBusInterface */
    private $messageBus;

    /** @var UnitOfWorkInterface */
    private $unitOfWork;

    public function __construct(UnitOfWorkInterface $unitOfWork, MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
        $this->unitOfWork = $unitOfWork;
    }

    public function handle(EnvelopeInterface $envelope): bool
    {
        $commandMessage = $envelope->getMessage();
        $handlerName = (new \ReflectionClass($commandMessage))->getShortName();
        $handlerMethod = "handle".ucfirst($handlerName);
        $handler = [ $this, $handlerMethod ];
        if (!is_callable($handler)) {
            throw new \Exception("Handler '$handlerMethod' isn't callable on ".static::class);
        }
        return call_user_func($handler, $commandMessage, $envelope->getMetadata());
    }

    protected function commit(AggregateRootInterface $aggregateRoot, Metadata $metadata): bool
    {
        $committed = false;
        foreach ($this->unitOfWork->commit($aggregateRoot, $metadata) as $newCommit) {
            if ($this->dispatch($newCommit) && !$committed) {
                $committed = true;
            }
        }
        return $committed;
    }

    protected function checkout(
        AggregateIdInterface $aggregateId,
        AggregateRevision $revision = null
    ): AggregateRootInterface {
        return $this->unitOfWork->checkout($aggregateId, $revision);
    }

    private function dispatch(CommitInterface $commit): bool
    {
        $commitPublished = $this->messageBus->publish($commit, "commits");
        // @todo might wanna send these from within the commit channel to guarantee order?
        foreach ($commit->getEventLog() as $committedEvent) {
            $this->messageBus->publish($committedEvent, "events");
        }
        return $commitPublished;
    }
}
