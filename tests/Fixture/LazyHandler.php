<?php

namespace Accordia\Tests\Cqrs\Fixture;

use Accordia\MessageBus\Channel\Subscription\MessageHandler\MessageHandlerInterface;
use Accordia\MessageBus\EnvelopeInterface;

final class LazyHandler implements MessageHandlerInterface
{
    /**
     * @var callable
     */
    private $factory;

    /**
     * @var
     */
    private $handler;

    /**
     * @param callable $factory
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param EnvelopeInterface $envelope
     * @return bool
     */
    public function handle(EnvelopeInterface $envelope): bool
    {
        if (!$this->handler) {
            $this->handler = call_user_func($this->factory);
        }
        return $this->handler->handle($envelope);
    }
}
