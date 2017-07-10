<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing;

use Daikon\EventSourcing\Aggregate\DomainEventInterface;
use Daikon\Tests\EventSourcing\Aggregate\Mock\PizzaWasBaked;
use PHPUnit\Framework\TestCase;

final class DomainEventTest extends TestCase
{
    public function testFromArray()
    {
        $pizzaWasBaked = PizzaWasBaked::fromArray([
            'aggregateId' => 'pizza-42-6-23',
            'aggregateRevision' => 1,
            'ingredients' => [ 'mushrooms', 'tomatoes', 'onions' ]
        ]);
        $this->assertInstanceOf(DomainEventInterface::class, $pizzaWasBaked);
    }

    public function testToArrayRoundTrip()
    {
        $pizza = [
            'aggregateId' => 'pizza-42-6-23',
            'aggregateRevision' => 1,
            'ingredients' => [ 'mushrooms', 'tomatoes', 'onions' ]
        ];
        $this->assertEquals($pizza, PizzaWasBaked::fromArray($pizza)->toArray());
    }
}
