<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\Command\Login;

use App\IdentityAccess\Domain\Exception\AccountNotActiveException;
use App\IdentityAccess\Domain\Exception\InvalidCredentialsException;
use App\IdentityAccess\Domain\Repository\UserRepositoryInterface;
use App\IdentityAccess\Domain\Security\AccessTokenIssuerInterface;
use App\IdentityAccess\Domain\Security\PasswordHasherInterface;
use App\IdentityAccess\Domain\Security\RefreshTokenStoreInterface;
use App\Shared\Domain\ValueObject\Email;

final readonly class LoginHandler
{
    private const int ACCESS_TOKEN_TTL_SECONDS = 900; // 15 min, cf. cahier des charges §3.3

    public function __construct(
        private UserRepositoryInterface $users,
        private PasswordHasherInterface $passwordHasher,
        private AccessTokenIssuerInterface $accessTokenIssuer,
        private RefreshTokenStoreInterface $refreshTokens,
    ) {}

    public function __invoke(LoginCommand $command): LoginResult
    {
        $user = $this->users->ofEmail(new Email($command->email));

        // Message volontairement identique que l'email existe ou non,
        // et que ce soit l'email ou le mot de passe qui soit incorrect —
        // évite l'énumération de comptes.
        if (null === $user || !$this->passwordHasher->verify($command->plainPassword, $user->passwordHash())) {
            throw new InvalidCredentialsException('Email ou mot de passe incorrect.');
        }

        // À partir d'ici, l'identité est confirmée : un message plus précis
        // ne présente plus de risque d'énumération.
        if (!$user->status()->canAuthenticate()) {
            throw new AccountNotActiveException(sprintf(
                'Ce compte ne peut pas se connecter dans son état actuel (%s).',
                $user->status()->value,
            ));
        }

        $accessToken = $this->accessTokenIssuer->issueFor($user);
        $refreshToken = $this->refreshTokens->issue($user->id(), $command->deviceFingerprint);

        return new LoginResult($accessToken, $refreshToken, self::ACCESS_TOKEN_TTL_SECONDS);
    }
}
