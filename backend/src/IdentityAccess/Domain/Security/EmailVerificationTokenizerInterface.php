<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Security;

use App\IdentityAccess\Domain\ValueObject\UserId;

/**
 * Port du Domain : génération et validation du lien signé de vérification
 * d'email (cahier des charges §5.2 : "lien signé à usage unique, expiration
 * 24h"). Aucune référence à un mécanisme de signature concret ici.
 */
interface EmailVerificationTokenizerInterface
{
    public function generateFor(UserId $userId): string;

    /**
     * @throws \App\IdentityAccess\Domain\Exception\InvalidVerificationTokenException
     */
    public function verify(UserId $userId, string $token): void;
}
