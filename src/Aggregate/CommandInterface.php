<?php

namespace Daikon\Cqrs\Aggregate;

use Daikon\MessageBus\MessageInterface;

interface CommandInterface extends MessageInterface
{
    public static function getAggregateRootClass(): string;

    public function getAggregateId(): AggregateIdInterface;

    public function getKnownAggregateRevision(): ?AggregateRevision;

    public function hasKnownAggregateRevision(): bool;
}
