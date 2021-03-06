<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate\Command;

use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\AggregateRootInterface;
use Daikon\EventSourcing\EventStore\UnitOfWorkInterface;
use Daikon\Interop\RuntimeException;
use Daikon\MessageBus\Channel\Subscription\MessageHandler\MessageHandlerInterface;
use Daikon\MessageBus\EnvelopeInterface;
use Daikon\MessageBus\MessageBusInterface;
use Daikon\Metadata\MetadataInterface;
use ReflectionClass;

abstract class CommandHandler implements MessageHandlerInterface
{
    private const COMMITS_CHANNEL = 'commits';

    private MessageBusInterface $messageBus;

    private UnitOfWorkInterface $unitOfWork;

    public function __construct(UnitOfWorkInterface $unitOfWork, MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
        $this->unitOfWork = $unitOfWork;
    }

    public function handle(EnvelopeInterface $envelope): void
    {
        $commandMessage = $envelope->getMessage();
        $handlerName = (new ReflectionClass($commandMessage))->getShortName();
        $handlerMethod = 'handle'.ucfirst($handlerName);
        $handler = [$this, $handlerMethod];
        if (!is_callable($handler)) {
            throw new RuntimeException(
                sprintf("Handler '%s' is not callable on '%s'.", $handlerMethod, static::class)
            );
        }
        $this->commit(...$handler($commandMessage, $envelope->getMetadata()));
    }

    protected function commit(AggregateRootInterface $aggregateRoot, MetadataInterface $metadata): void
    {
        $newCommits = $this->unitOfWork->commit($aggregateRoot, $metadata);
        foreach ($newCommits as $newCommit) {
            $this->messageBus->publish($newCommit, self::COMMITS_CHANNEL, $metadata);
        }
    }

    protected function checkout(
        AggregateIdInterface $aggregateId,
        AggregateRevision $knownAggregateRevision
    ): AggregateRootInterface {
        return $this->unitOfWork->checkout($aggregateId, $knownAggregateRevision);
    }
}
