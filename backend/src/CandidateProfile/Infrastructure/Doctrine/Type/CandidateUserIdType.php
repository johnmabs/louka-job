<?php

declare(strict_types=1);

namespace App\CandidateProfile\Infrastructure\Doctrine\Type;

use App\CandidateProfile\Domain\ValueObject\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class CandidateUserIdType extends GuidType
{
    public const string NAME = 'candidate_user_id';

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
