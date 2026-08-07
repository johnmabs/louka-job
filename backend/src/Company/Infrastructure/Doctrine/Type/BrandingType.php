<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Doctrine\Type;

use App\Company\Domain\ValueObject\Branding;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

final class BrandingType extends JsonType
{
    public const string NAME = 'company_branding';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Branding
    {
        $decoded = parent::convertToPHPValue($value, $platform);

        if (null === $decoded) {
            return null;
        }

        return $decoded instanceof Branding ? $decoded : Branding::fromArray($decoded);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        $array = $value instanceof Branding ? $value->toArray() : $value;

        return parent::convertToDatabaseValue($array, $platform);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
