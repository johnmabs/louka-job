<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Http\Controller;

use App\IdentityAccess\Application\Command\RefreshToken\RefreshTokenCommand;
use App\IdentityAccess\Application\Command\RefreshToken\RefreshTokenHandler;
use App\IdentityAccess\Domain\Exception\AccountNotActiveException;
use App\IdentityAccess\Domain\Exception\InvalidRefreshTokenException;
use App\IdentityAccess\Domain\Exception\RefreshTokenReuseDetectedException;
use App\IdentityAccess\Domain\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class RefreshTokenController
{
    public function __construct(
        private readonly RefreshTokenHandler $handler,
    ) {}

    #[Route('/api/v1/auth/refresh', name: 'identity_access_refresh', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $payload = $request->toArray();
        } catch (\JsonException) {
            return new JsonResponse(['detail' => 'Corps de requête JSON invalide.'], 400);
        }

        $refreshToken = $payload['refresh_token'] ?? null;

        if (!is_string($refreshToken) || '' === $refreshToken) {
            return new JsonResponse(['detail' => 'Le champ "refresh_token" est requis.'], 400);
        }

        $deviceFingerprint = hash(
            'sha256',
            $request->headers->get('User-Agent', 'unknown') . '|' . $request->getClientIp(),
        );

        try {
            $result = ($this->handler)(new RefreshTokenCommand($refreshToken, $deviceFingerprint));
        } catch (InvalidRefreshTokenException | UserNotFoundException $e) {
            return new JsonResponse(['detail' => $e->getMessage()], 401);
        } catch (RefreshTokenReuseDetectedException $e) {
            // TODO: journaliser cet incident de sécurité (rejeu détecté)
            // une fois l'observabilité/logging structuré en place.
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
