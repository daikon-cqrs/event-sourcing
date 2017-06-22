<?php

namespace Accordia\Cqrs\EventStore;

interface PersistenceAdapterInterface
{
    /**
     * @param CommitStreamId $streamId
     * @param CommitStreamRevision|null $revision
     * @return CommitStreamInterface
     */
    public function loadStream(CommitStreamId $streamId, CommitStreamRevision $revision = null): CommitStreamInterface;

    /**
     * @param CommitStreamInterface $stream
     * @param CommitStreamRevision $storeHead
     * @return StoreResultInterface
     */
    public function storeStream(
        CommitStreamInterface $stream,
        CommitStreamRevision $storeHead
    ): StoreResultInterface;
}
