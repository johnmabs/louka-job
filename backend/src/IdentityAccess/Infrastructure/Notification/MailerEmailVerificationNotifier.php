<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Notification;

use App\IdentityAccess\Domain\Notification\EmailVerificationNotifierInterface;
use App\IdentityAccess\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final class MailerEmailVerificationNotifier implements EmailVerificationNotifierInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $frontendUrl,
        private readonly string $senderAddress,
    ) {}

    public function notify(UserId $userId, Email $email, string $verificationToken): void
    {
        $link = sprintf(
            '%s/verify-email?id=%s&token=%s',
            rtrim($this->frontendUrl, '/'),
            $userId->toString(),
            urlencode($verificationToken),
        );

        $message = (new TemplatedEmail())
            ->from(new Address($this->senderAddress, 'HireFlow'))
            ->to($email->value())
            ->subject('Confirmez votre adresse email')
            ->htmlTemplate('identity_access/emails/verify_email.html.twig')
            ->context(['verificationLink' => $link]);

        $this->mailer->send($message);
    }
}
