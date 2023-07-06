<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class SymfonyMailer implements MailerInterface
{
    public function __construct(
        private readonly SymfonyMailerInterface $mailer,
        private readonly TranslatorInterface $translator,
        private readonly string $senderEmail,
        private readonly string $senderName,
    ) {
    }

    public function getSender(): Address
    {
        return new Address($this->senderEmail, $this->senderName);
    }

    public function send(Email $email): bool
    {
        $mail = (new TemplatedEmail())
            ->from($this->getSender())
            ->to($email->getReceiverEmail())
            ->subject($this->translator->trans(
                $email->getSubject(),
                $email->getParameters(),
                'emails',
            ))
            ->htmlTemplate($email->getHtmlTemplate())
            ->textTemplate($email->getTextTemplate())
            ->context($email->getParameters())
        ;

        try {
            $this->mailer->send($mail);
        } catch (TransportExceptionInterface) {
            return false;
        }

        return true;
    }
}
