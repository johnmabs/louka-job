<?php

declare(strict_types=1);

namespace App\CandidateProfile\Infrastructure\Doctrine\Type;

use App\CandidateProfile\Domain\ValueObject\Availability;
use App\CandidateProfile\Domain\ValueObject\AvailabilityStatus;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

final class AvailabilityType extends JsonType
{
    public const string NAME = 'candidate_availability';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Availability
    {
        $decoded = parent::convertToPHPValue($value, $platform);

        if (null === $decoded) {
            return null;
        }

        if ($decoded instanceof Availability) {
            return $decoded;
        }

        $status = AvailabilityStatus::from($decoded['status']);

        return match ($status) {
            AvailabilityStatus::Immediate => Availability::immediate(),
            AvailabilityStatus::EmployedOpenToOffers => Availability::employedOpenToOffers(),
            AvailabilityStatus::ScheduledDate => Availability::scheduledAt(new \DateTimeImmutable($decoded['availableFrom'])),
        };
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof Availability) {
            $array = [
                'status' => $value->status->value,
                'availableFrom' => $value->availableFrom?->format(DATE_ATOM),
            ];
        } else {
            $array = $value;
        }

        return parent::convertToDatabaseValue($array, $platform);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
