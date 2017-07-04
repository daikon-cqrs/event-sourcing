<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\Projection;

use Daikon\Cqrs\Aggregate\DomainEventInterface;
use Daikon\Entity\Entity\EntityInterface;

interface ProjectionInterface extends EntityInterface
{
    public function project(DomainEventInterface $domainEvent): ProjectionInterface;
}
