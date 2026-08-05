<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\Command\RefreshToken;

use App\IdentityAccess\Application\Command\Login\LoginResult;
use App\IdentityAccess\Domain\Exception\AccountNotActiveException;
use App\IdentityAccess\Domain\Exception\UserNotFoundException;
use App\IdentityAccess\Domain\Repository\UserRepositoryInterface;
use App\IdentityAccess\Domain\Security\AccessTokenIssuerInterface;
use App\IdentityAccess\Domain\Security\RefreshTokenStoreInterface;

final readonly class RefreshTokenHandler
{
    private const int ACCESS_TOKEN_TTL_SECONDS = 900;

    public function __construct(
        private UserRepositoryInterface $users,
        private AccessTokenIssuerInterface $accessTokenIssuer,
        private RefreshTokenStoreInterface $refreshTokens,
    ) {}

    public function __invoke(RefreshTokenCommand $command): LoginResult
    {
        // Lève InvalidRefreshTokenException ou RefreshTokenReuseDetectedException
        // si le token est invalide/expiré/déjà utilisé — la chaîne est alors
        // révoquée automatiquement par l'Adapter.
        $rotation = $this->refreshTokens->rotate($command->refreshToken, $command->deviceFingerprint);

        $user = $this->users->ofId($rotation->userId);

        if (null === $user) {
            throw new UserNotFoundException('Compte introuvable.');
        }

        if (!$user->status()->canAuthenticate()) {
            throw new AccountNotActiveException(sprintf(
                'Ce compte ne peut pas se connecter dans son état actuel (%s).',
                $user->status()->value,
            ));
        }

        $accessToken = $this->accessTokenIssuer->issueFor($user);

        return new LoginResult($accessToken, $rotation->newPlainToken, self::ACCESS_TOKEN_TTL_SECONDS);
    }
}
