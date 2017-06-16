<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\Cqrs\Aggregate\Revision;

interface PersistenceAdapterInterface
{
    /**
     * @param StreamId $streamId
     * @param Revision|null $revision
     * @return CommitStreamInterface
     */
    public function loadStream(StreamId $streamId, Revision $revision = null): CommitStreamInterface;

    /**
     * @param CommitStreamInterface $stream
     * @param Revision $storeHead
     * @return bool
     */
    public function storeStream(CommitStreamInterface $stream, Revision $storeHead): bool;
}
