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
     * @return Revision|null
     */
    public function getExpectedRevision(): ?Revision;

    /**
     * @return bool
     */
    public function hasExpectedRevision(): bool;
}
