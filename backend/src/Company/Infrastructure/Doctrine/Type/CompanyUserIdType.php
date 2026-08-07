<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Doctrine\Type;

use App\Company\Domain\ValueObject\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

/**
 * Nom de type Doctrine distinct de "user_id" (déjà pris par IdentityAccess\UserIdType)
 * même si la structure est identique — les deux types restent indépendants
 * pour ne pas coupler les deux Bounded Contexts au niveau Infrastructure.
 */
final class CompanyUserIdType extends GuidType
{
    public const string NAME = 'company_user_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?UserId
    {
        if ($value === null || $value instanceof UserId) {
            return $value;
        }

        return UserId::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof UserId ? $value->toString() : (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
