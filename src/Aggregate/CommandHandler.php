<?php

namespace Daikon\Cqrs\Aggregate;

use Daikon\MessageBus\Channel\Subscription\MessageHandler\MessageHandlerInterface;
use Daikon\MessageBus\EnvelopeInterface;
use Daikon\MessageBus\MessageBusInterface;
use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Cqrs\EventStore\CommitInterface;
use Daikon\Cqrs\EventStore\UnitOfWorkInterface;

abstract class CommandHandler implements MessageHandlerInterface
{
    private $messageBus;

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
