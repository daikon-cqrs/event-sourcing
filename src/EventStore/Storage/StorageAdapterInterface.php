<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\EventStore\Storage;

use Daikon\EventSourcing\EventStore\Commit\CommitSequenceInterface;

interface StorageAdapterInterface
{
    public function load(string $identifier, string $from = null, string $to = null): CommitSequenceInterface;

    public function append(string $identifier, array $data): void;

    public function purge(string $identifier): void;
}
