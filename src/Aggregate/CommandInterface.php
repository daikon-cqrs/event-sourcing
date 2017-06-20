<?php

namespace Accordia\Cqrs\Aggregate;

use Accordia\MessageBus\MessageInterface;

interface CommandInterface extends MessageInterface
{
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
