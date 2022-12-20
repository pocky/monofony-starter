<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mailer;

use Symfony\Component\Mime\Address;

final class Email
{
    public function __construct(
        private readonly Address $receiverEmail,
        private readonly string $subject,
        private readonly string $textTemplate,
        private readonly string $htmlTemplate,
        private readonly array $parameters,
    ) {
    }

    public function getReceiverEmail(): Address
    {
        return $this->receiverEmail;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getTextTemplate(): string
    {
        return $this->textTemplate;
    }

    public function getHtmlTemplate(): string
    {
        return $this->htmlTemplate;
    }

    /**
     * @return mixed[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
