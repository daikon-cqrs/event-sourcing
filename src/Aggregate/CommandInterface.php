<?php

namespace Daikon\Cqrs\Aggregate;

use Daikon\MessageBus\MessageInterface;

interface CommandInterface extends MessageInterface
{
    public static function getAggregateRootClass(): string;

    /**
     * @return AggregateIdInterface
     */
    public function getAggregateId(): AggregateIdInterface;

    /**
     * @return AggregateRevision|null
     */
    public function getKnownAggregateRevision(): ?AggregateRevision;

    /**
     * @return bool
     */
    public function hasKnownAggregateRevision(): bool;
}
