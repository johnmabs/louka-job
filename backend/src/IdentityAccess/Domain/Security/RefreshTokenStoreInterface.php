<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Security;

use App\IdentityAccess\Domain\ValueObject\UserId;

/**
 * Port du Domain : émission, rotation et révocation des refresh tokens.
 * Aucune référence à Valkey/Redis ici — l'implémentation concrète du
 * stockage (et le mécanisme de détection de rejeu) est un détail
 * d'Infrastructure.
 */
interface RefreshTokenStoreInterface
{
    public function issue(UserId $userId, string $deviceFingerprint): string;

    /**
     * @throws \App\IdentityAccess\Domain\Exception\InvalidRefreshTokenException
     * @throws \App\IdentityAccess\Domain\Exception\RefreshTokenReuseDetectedException
     */
    public function rotate(string $plainToken, string $deviceFingerprint): RefreshTokenRotationResult;

    /**
     * Révoque toutes les chaînes actives d'un utilisateur (déconnexion
     * globale, réinitialisation de mot de passe — cahier des charges §6.3).
     */
    public function revokeAllForUser(UserId $userId): void;
}
