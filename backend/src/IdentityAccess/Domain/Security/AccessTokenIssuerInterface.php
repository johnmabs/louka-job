<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Security;

use App\IdentityAccess\Domain\Model\User;

/**
 * Port du Domain : émission du token d'accès (JWT RS256, cahier des
 * charges §3.3). Aucune référence à Lexik ou à Symfony Security ici.
 */
interface AccessTokenIssuerInterface
{
    public function issueFor(User $user): string;
}
