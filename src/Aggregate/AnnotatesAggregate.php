<?php
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

trait AnnotatesAggregate
{
    private static function getAnnotatedId(): string
    {
        $classReflection = new \ReflectionClass(static::class);
        if (!preg_match("#@id\((?<id>\w+)#", $classReflection->getDocComment(), $matches)) {
            throw new \RuntimeException('Missing @id annotation on '.static::class);
        }
        return trim($matches['id']);
    }

    private static function getAnnotatedRevision()
    {
        $classReflection = new \ReflectionClass(static::class);
        if (!preg_match('#@rev\((?<rev>\w+)#', $classReflection->getDocComment(), $matches)) {
            throw new \RuntimeException('Missing @rev annotation on '.static::class);
        }
        return trim($matches['rev']);
    }

    public function getAggregateId(): AggregateIdInterface
    {
        return $this->{static::getAnnotatedId()};
    }
}
