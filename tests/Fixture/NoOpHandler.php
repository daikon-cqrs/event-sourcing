<?php

namespace Daikon\Tests\EventSourcing\Fixture;

use Daikon\MessageBus\Channel\Subscription\MessageHandler\MessageHandlerInterface;
use Daikon\MessageBus\EnvelopeInterface;

final class NoOpHandler implements MessageHandlerInterface
{
    public function handle(EnvelopeInterface $envelope): bool
    {
        // echo PHP_EOL . "envelope: " . json_encode($envelope->toArray()) . PHP_EOL;
        return true;
    }
}
