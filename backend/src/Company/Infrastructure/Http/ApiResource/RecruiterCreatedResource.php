<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Http\ApiResource;

use ApiPlatform\Metadata\ApiProperty;

final class RecruiterCreatedResource
{
    #[ApiProperty(identifier: true)]
    public string $userId = '';
    public string $email = '';
    public string $role = '';
}
