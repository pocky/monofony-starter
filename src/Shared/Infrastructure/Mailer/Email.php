<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mailer;

use Symfony\Component\Mime\Address;

final readonly class Email
{
    public function __construct(
        private Address $receiverEmail,
        private string $subject,
        private string $textTemplate,
        private string $htmlTemplate,
        private array $parameters,
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
