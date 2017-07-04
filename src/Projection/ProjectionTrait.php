<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\Projection;

use Daikon\Cqrs\Aggregate\DomainEventInterface;

trait ProjectionTrait
{
    public function project(DomainEventInterface $domainEvent): ProjectionInterface
    {
        return $this->invokeEventHandler($domainEvent);
    }

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
