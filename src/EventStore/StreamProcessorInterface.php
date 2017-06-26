<?php

namespace Daikon\Cqrs\EventStore;

interface StreamProcessorInterface
{
    public function process(CommitStream $commitStream): CommitStream;
}
