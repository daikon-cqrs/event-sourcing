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

final class AggregateAlias
{
    /** @var string */
    private $alias;

    public static function fromNative(string $alias): AggregateAlias
    {
        return new static(trim($alias));
    }

    public function toNative(): string
    {
        return $this->alias;
    }

    public function equals(AggregateAlias $alias): bool
    {
        return $this->alias === $alias->toNative();
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
