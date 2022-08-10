<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mailer;

use Symfony\Component\Mime\Address;

interface MailerInterface
{
    public function send(Email $email): bool;

    public function getSender(): Address;
}
