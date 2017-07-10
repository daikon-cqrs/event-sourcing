<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing\Aggregate\Mock;

use Daikon\EventSourcing\Aggregate\CommandHandler;
use Daikon\MessageBus\Metadata\Metadata;

/**
 * @codeCoverageIgnore
 */
final class BakePizzaHandler extends CommandHandler
{
    protected function handleBakePizza(BakePizza $bakePizza, Metadata $metadata)
    {
        return $this->commit(Pizza::bake($bakePizza), $metadata);
    }
}
