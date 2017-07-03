<?php

namespace Daikon\Cqrs\EventStore;

interface StreamStoreInterface
{
    public function checkout(
        CommitStreamId $streamId,
        CommitStreamRevision $from = null,
        CommitStreamRevision $to = null
    ): CommitStreamInterface;

    public function commit(
        CommitStreamInterface $stream,
        CommitStreamRevision $storeHead
    ): StoreResultInterface;
}
