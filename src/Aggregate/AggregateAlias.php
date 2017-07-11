<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

use Assert\Assertion;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class AggregateAlias implements AggregateIdInterface
{
    /** @var string */
    private $alias;

    public static function fromNative($alias): ValueObjectInterface
    {
        Assertion::string($alias);
        return new static(trim($alias));
    }

    public static function makeEmpty(): ValueObjectInterface
    {
        throw new \Exception('Creating empty aggregate aliases is not supported.');
    }

    public function toNative()
    {
        return $this->alias;
    }

    public function equals(ValueObjectInterface $alias): bool
    {
        Assertion::isInstanceOf($alias, static::class);
        return $this->alias === $alias->toNative();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return $this->alias;
    }

    private function __construct(string $alias)
    {
        Assertion::notEmpty($alias);
        $this->alias = $alias;
    }
}
