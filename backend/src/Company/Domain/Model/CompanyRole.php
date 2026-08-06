<?php

declare(strict_types=1);

namespace App\Company\Domain\Model;

/**
 * RBAC organisation (cahier des charges §7.2) — distinct des ROLE_* Symfony
 * globaux du module IdentityAccess. Un CompanyMember porte un seul rôle.
 */
enum CompanyRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Recruiter = 'recruiter';
    case Viewer = 'viewer';

    /**
     * Réservé à owner/admin (cahier des charges §7.3 : un recruiter ne
     * peut jamais inviter/supprimer un collaborateur).
     */
    public function canManageMembers(): bool
    {
        return match ($this) {
            self::Owner, self::Admin => true,
            self::Recruiter, self::Viewer => false,
        };
    }

    /**
     * Réservé au owner (transfert de propriété, suppression du compte
     * entreprise, facturation).
     */
    public function canManageBilling(): bool
    {
        return $this === self::Owner;
    }
}
