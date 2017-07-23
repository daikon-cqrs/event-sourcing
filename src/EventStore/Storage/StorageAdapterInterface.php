<?php

namespace Daikon\EventSourcing\EventStore\Storage;

use Daikon\EventSourcing\EventStore\Commit\CommitSequenceInterface;

interface StorageAdapterInterface
{
    public function load(string $identifier): CommitSequenceInterface;

    public function append(string $identifier, array $data): void;

    public function purge(string $identifier): void;
}
