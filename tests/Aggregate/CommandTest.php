<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing;

use Daikon\EventSourcing\Aggregate\CommandInterface;
use Daikon\Tests\EventSourcing\Aggregate\Mock\BakePizza;
use PHPUnit\Framework\TestCase;

final class CommandTest extends TestCase
{
    public function testFromArray()
    {
        $bakePizza = BakePizza::fromArray([
            'aggregateId' => 'pizza-42-6-23',
            'knownAggregateRevision' => 0,
            'ingredients' => [ 'mushrooms', 'tomatoes', 'onions' ]
        ]);
        $this->assertInstanceOf(CommandInterface::class, $bakePizza);
    }

    public function testToArrayRoundTrip()
    {
        $pizza = [
            'aggregateId' => 'pizza-42-6-23',
            'knownAggregateRevision' => 0,
            'ingredients' => [ 'mushrooms', 'tomatoes', 'onions' ]
        ];
        $this->assertEquals($pizza, BakePizza::fromArray($pizza)->toArray());
    }
}
