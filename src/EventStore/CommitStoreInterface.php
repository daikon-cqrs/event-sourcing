<?php

namespace Daikon\Cqrs\EventStore;

interface StreamStoreInterface
{
    public function loadStream(CommitStreamId $streamId, CommitStreamRevision $revision = null): CommitStreamInterface;

    public function storeStream(
        CommitStreamInterface $stream,
        CommitStreamRevision $storeHead
    ): StoreResultInterface;
}
