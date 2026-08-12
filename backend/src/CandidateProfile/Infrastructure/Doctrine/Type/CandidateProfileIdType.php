<?php

declare(strict_types=1);

namespace App\CandidateProfile\Infrastructure\Doctrine\Type;

use App\CandidateProfile\Domain\ValueObject\CandidateProfileId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class CandidateProfileIdType extends GuidType
{
    public const string NAME = 'candidate_profile_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?CandidateProfileId
    {
        if ($value === null || $value instanceof CandidateProfileId) {
            return $value;
        }

        return CandidateProfileId::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof CandidateProfileId ? $value->toString() : (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
