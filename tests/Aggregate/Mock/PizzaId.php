<?php
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing\Aggregate\Mock;

use Assert\Assertion;
use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateIdTrait;
use Daikon\Interop\ValueObjectInterface;

final class PizzaId implements AggregateIdInterface
{
    use AggregateIdTrait;

    const PATTERN = '#^pizza-.+$#';
}
