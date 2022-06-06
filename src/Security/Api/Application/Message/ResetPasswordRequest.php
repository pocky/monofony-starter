<?php

declare(strict_types=1);

namespace App\Security\Api\Application\Message;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ResetPasswordRequest
{
    public function __construct(#[NotBlank(message: 'sylius.user.email.not_blank')] #[Groups(groups: [
        'customer:write',
    ])] public ?string $email = null)
    {
    }
}
