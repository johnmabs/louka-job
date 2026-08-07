<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Doctrine\Type;

use App\Company\Domain\ValueObject\CompanyId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class CompanyIdType extends GuidType
{
    public const string NAME = 'company_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?CompanyId
    {
        if ($value === null || $value instanceof CompanyId) {
            return $value;
        }

        return CompanyId::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof CompanyId ? $value->toString() : (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
