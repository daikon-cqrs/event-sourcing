<?php

namespace Accordia\Cqrs\Projection;

use Accordia\Cqrs\Aggregate\DomainEventInterface;

trait ProjectionTrait
{
    /**
     * @param DomainEventInterface $domainEvent
     * @return ProjectionInterface
     */
    public function project(DomainEventInterface $domainEvent): ProjectionInterface
    {
        return $this->invokeEventHandler($domainEvent);
    }

    /**
     * @param DomainEventInterface $event
     * @return ProjectionInterface
     * @throws \Exception
     */
    private function invokeEventHandler(DomainEventInterface $event): ProjectionInterface
    {
        $handlerName = preg_replace("/Event$/", "", (new \ReflectionClass($event))->getShortName());
        $handlerMethod = "when".ucfirst($handlerName);
        $handler = [ $this, $handlerMethod ];
        if (!is_callable($handler)) {
            throw new \Exception("Handler '$handlerMethod' isn't callable on ".static::class);
        }
        return call_user_func($handler, $event);
    }
}
