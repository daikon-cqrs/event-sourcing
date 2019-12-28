<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Tests\EventSourcing\Aggregate\Mock;

use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateIdTrait;

final class PizzaId implements AggregateIdInterface
{
    use AggregateIdTrait;

    const PATTERN = '#^pizza-.+$#';
}
