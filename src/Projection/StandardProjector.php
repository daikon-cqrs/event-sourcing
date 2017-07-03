<?php

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
