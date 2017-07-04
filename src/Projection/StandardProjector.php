<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\Projection;

use Daikon\MessageBus\Channel\Subscription\MessageHandler\MessageHandlerInterface;
use Daikon\MessageBus\EnvelopeInterface;
use Daikon\Entity\EntityType\EntityTypeInterface;

class StandardProjector implements MessageHandlerInterface
{
    private $projectionType;

    public function __construct(EntityTypeInterface $projectionType)
    {
        $this->projectionType = $projectionType;
    }

    public function handle(EnvelopeInterface $envelope): bool
    {
        $commit = $envelope->getMessage();
        // @todo load projection by stream-id
        $projection = $this->projectionType->makeEntity();
        foreach ($commit->getEventLog() as $domainEvent) {
            // @todo this will work for all but mirrored-reference values,
            // which would need to be loaded (recursively) somehow
            $projection = $projection->project($domainEvent);
        }
        // @todo store projection
        // var_dump($projection->toArray());
        return true;
    }
}
