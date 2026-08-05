<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Http\Controller;

use App\IdentityAccess\Infrastructure\Security\SecurityUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/**
 * Route de test du circuit d'authentification JWT + RBAC. Utile pour
 * valider le firewall/UserProvider ; deviendra un vrai endpoint "mon profil"
 * plus tard.
 */
final class MeController
{
    #[Route('/api/v1/me', name: 'identity_access_me', methods: ['GET'])]
    public function __invoke(#[CurrentUser] SecurityUser $user): JsonResponse
    {
        return new JsonResponse([
            'id' => $user->id()->toString(),
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }
}
