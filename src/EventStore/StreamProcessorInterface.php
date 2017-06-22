<?php

namespace Accordia\Cqrs\EventStore;

interface StreamProcessorInterface
{
    public function process(CommitStream $commitStream): CommitStream;
}
