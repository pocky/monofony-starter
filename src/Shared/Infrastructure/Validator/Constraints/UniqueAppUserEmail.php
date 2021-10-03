<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class UniqueAppUserEmail extends Constraint
{
    /** @var string */
    public $message = 'sylius.user.email.unique';
}
