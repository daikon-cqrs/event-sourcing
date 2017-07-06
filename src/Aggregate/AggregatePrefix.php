<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\Aggregate;

use Assert\Assertion;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class AggregatePrefix implements AggregateIdInterface
{
    /** @var string */
    private $prefix;

    public static function fromNative($prefix): ValueObjectInterface
    {
        return new static(trim($prefix));
    }

    public static function makeEmpty(): ValueObjectInterface
    {
        throw new \Exception("Creating empty aggregate prefixes is not supported.");
    }

    public static function fromFqcn(string $fqcn): ValueObjectInterface
    {
        Assertion::classExists($fqcn);

        $parts = explode('\\', $fqcn, 4);
        return static::fromNative(
            sprintf(
                '%s.%s.%s',
                static::asSnakeCase($parts[0]),
                static::asSnakeCase($parts[1]),
                static::asSnakeCase($parts[2])
            )
        );
    }

    public function toNative()
    {
        return $this->prefix;
    }

    public function equals(ValueObjectInterface $streamPrefix): bool
    {
        Assertion::isInstanceOf($streamPrefix, static::class);
        return $this->prefix === $streamPrefix->toNative();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return $this->prefix;
    }

    private static function asSnakeCase(string $value): string
    {
        return ctype_lower($value)
            ? $value
            : mb_strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $value));
    }

    private function __construct(string $prefix)
    {
        Assertion::notEmpty($prefix);
        $this->prefix = $prefix;
    }
}
