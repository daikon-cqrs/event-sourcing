<?php

namespace Daikon\Tests\Cqrs\Fixture;

use Daikon\MessageBus\Channel\Subscription\MessageHandler\MessageHandlerInterface;
use Daikon\MessageBus\EnvelopeInterface;

final class NoOpHandler implements MessageHandlerInterface
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
