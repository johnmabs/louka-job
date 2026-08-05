<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Security;

use App\IdentityAccess\Domain\Model\User;
use App\IdentityAccess\Domain\Security\AccessTokenIssuerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

final class LexikAccessTokenIssuer implements AccessTokenIssuerInterface
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager,
    ) {}

    public function issueFor(User $user): string
    {
        return $this->jwtManager->create(new SecurityUser($user));
    }
}
