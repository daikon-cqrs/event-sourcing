<?php

namespace Daikon\Tests\EventSourcing\Fixture;

use Daikon\MessageBus\Channel\Subscription\MessageHandler\MessageHandlerInterface;
use Daikon\MessageBus\EnvelopeInterface;

final class LazyHandler implements MessageHandlerInterface
{
    /** @var callable */
    private $factory;

    /** @var MessageHandlerInterface */
    private $handler;

    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    public function handle(EnvelopeInterface $envelope): bool
    {
        if (!$this->handler) {
            $this->handler = call_user_func($this->factory);
        }
        return $this->handler->handle($envelope);
    }
}
