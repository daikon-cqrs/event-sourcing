<?php

namespace Accordia\Tests\Cqrs\Fixture;

use Accordia\MessageBus\Channel\Subscription\MessageHandler\MessageHandlerInterface;
use Accordia\MessageBus\EnvelopeInterface;

class NoOpHandler implements MessageHandlerInterface
{
    /**
     * @param EnvelopeInterface $envelope
     * @return bool
     */
    public function handle(EnvelopeInterface $envelope): bool
    {
        // echo PHP_EOL . "envelope: " . json_encode($envelope->toArray()) . PHP_EOL;
        return true;
    }
}
