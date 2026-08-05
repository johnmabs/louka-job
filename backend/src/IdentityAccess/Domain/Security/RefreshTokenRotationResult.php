<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Security;

use App\IdentityAccess\Domain\ValueObject\UserId;

final readonly class RefreshTokenRotationResult
{
    public function __construct(
        public UserId $userId,
        public string $newPlainToken,
    ) {}
}
