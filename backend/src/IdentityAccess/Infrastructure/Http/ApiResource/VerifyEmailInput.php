<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Http\ApiResource;

use Symfony\Component\Validator\Constraints as Assert;

final class VerifyEmailInput
{
    #[Assert\NotBlank]
    public string $token = '';
}
