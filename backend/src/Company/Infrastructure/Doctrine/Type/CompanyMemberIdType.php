<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Doctrine\Type;

use App\Company\Domain\ValueObject\CompanyMemberId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class CompanyMemberIdType extends GuidType
{
    public const string NAME = 'company_member_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?CompanyMemberId
    {
        if ($value === null || $value instanceof CompanyMemberId) {
            return $value;
        }

        return CompanyMemberId::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof CompanyMemberId ? $value->toString() : (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
