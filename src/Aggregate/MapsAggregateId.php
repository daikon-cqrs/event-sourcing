<?php
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

trait MapsAggregateId
{
    private static function getIdentifier(): string
    {
        $classReflection = new \ReflectionClass(static::class);
        preg_match("#@aggregateId\s+(?<identifier>.+)#", $classReflection->getDocComment(), $matches);
        if (!isset($matches['identifier'])) {
            throw new \RuntimeException('Missing @aggregateId annotation on '.static::class);
        }
        return trim($matches['identifier']);
    }

    public function getAggregateId(): AggregateIdInterface
    {
        return $this->{self::getIdentifier()};
    }
}
