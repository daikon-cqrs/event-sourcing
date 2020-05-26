<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Tests\EventSourcing\Aggregate\Mock;

use Daikon\EventSourcing\Aggregate\Command\CommandInterface;
use Daikon\EventSourcing\Aggregate\Command\CommandTrait;

/**
 * @codeCoverageIgnore
 */
final class BakePizza implements CommandInterface
{
    use CommandTrait;
    use BakeMessageTrait;
}
