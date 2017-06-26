<?php

namespace Daikon\Cqrs\EventStore;

final class NoopStreamProcessor implements StreamProcessorInterface
{
    public function process(CommitStream $commitStream): CommitStream
    {
        return $commitStream;
    }
}
