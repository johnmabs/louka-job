<?php

declare(strict_types=1);

namespace App\CandidateProfile\Infrastructure\Doctrine\Type;

use App\CandidateProfile\Domain\ValueObject\Location;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

final class LocationType extends JsonType
{
    public const string NAME = 'candidate_location';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Location
    {
        $decoded = parent::convertToPHPValue($value, $platform);

        if (null === $decoded) {
            return null;
        }

        return $decoded instanceof Location ? $decoded : Location::fromArray($decoded);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        $array = $value instanceof Location ? $value->toArray() : $value;

        return parent::convertToDatabaseValue($array, $platform);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
