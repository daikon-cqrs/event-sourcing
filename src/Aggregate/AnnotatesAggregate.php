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
    public function getAggregateId(): AggregateIdInterface
    {
        return $this->{static::getAnnotatedId()};
    }

    private static function getAnnotatedId(): string
    {
        $classReflection = new \ReflectionClass(static::class);
        foreach (static::getInheritanceTree($classReflection, true) as $curClass) {
            if (!($docComment = $curClass->getDocComment())) {
                continue;
            }
            preg_match("#@id\((?<id>\w+)#", $docComment, $matches);
            if (isset($matches['id'])) {
                return trim($matches['id']);
            }
        }

        throw new \RuntimeException('Missing @id annotation on '.static::class);
    }

    private static function getAnnotatedRevision(): string
    {
        $classReflection = new \ReflectionClass(static::class);
        foreach (static::getInheritanceTree($classReflection, true) as $curClass) {
            if (!($docComment = $curClass->getDocComment())) {
                continue;
            }
            preg_match('#@rev\((?<rev>\w+)#', $docComment, $matches);
            if (isset($matches['rev'])) {
                return trim($matches['rev']);
            }
        }

        throw new \RuntimeException('Missing @rev annotation on '.static::class);
    }
}
