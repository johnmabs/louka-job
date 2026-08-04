<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Notification;

use App\IdentityAccess\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Email;

/**
 * Port du Domain : notification de vérification d'email.
 * Aucune référence à Symfony Mailer ou à un template ici.
 */
interface EmailVerificationNotifierInterface
{
    public function notify(UserId $userId, Email $email, string $verificationToken): void;
}
