<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\Aggregate;

use Daikon\MessageBus\MessageInterface;

interface CommandInterface extends MessageInterface
{
    public static function getAggregateRootClass(): string;

    public function getAggregateId(): AggregateIdInterface;

    public function getKnownAggregateRevision(): ?AggregateRevision;

    public function hasKnownAggregateRevision(): bool;
}
