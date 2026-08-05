<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Http\Controller;

use App\IdentityAccess\Application\Command\Login\LoginCommand;
use App\IdentityAccess\Application\Command\Login\LoginHandler;
use App\IdentityAccess\Domain\Exception\AccountNotActiveException;
use App\IdentityAccess\Domain\Exception\InvalidCredentialsException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Pas une ressource API Platform : le login est une action, pas du CRUD
 * sur une entité. Controller Symfony classique, cf. cahier des charges §3.3.
 */
final class LoginController
{
    public function __construct(
        private readonly LoginHandler $handler,
    ) {}

    #[Route('/api/v1/auth/login', name: 'identity_access_login', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $payload = $request->toArray();
        } catch (\JsonException) {
            return new JsonResponse(['detail' => 'Corps de requête JSON invalide.'], 400);
        }

        $email = $payload['email'] ?? null;
        $password = $payload['password'] ?? null;

        if (!is_string($email) || !is_string($password) || '' === $email || '' === $password) {
            return new JsonResponse(['detail' => 'Les champs "email" et "password" sont requis.'], 400);
        }

        // Empreinte device calculée côté serveur (User-Agent + IP) — le
        // client n'a rien à fournir. Affinable plus tard (§6.5).
        $deviceFingerprint = hash(
            'sha256',
            $request->headers->get('User-Agent', 'unknown') . '|' . $request->getClientIp(),
        );

        try {
            $result = ($this->handler)(new LoginCommand($email, $password, $deviceFingerprint));
        } catch (InvalidCredentialsException $e) {
            return new JsonResponse(['detail' => $e->getMessage()], 401);
        } catch (AccountNotActiveException $e) {
            return new JsonResponse(['detail' => $e->getMessage()], 403);
        }

        return new JsonResponse([
            'access_token' => $result->accessToken,
            'refresh_token' => $result->refreshToken,
            'expires_in' => $result->expiresIn,
        ]);
    }
}
