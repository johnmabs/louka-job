<?php

declare(strict_types=1);

namespace App\CandidateProfile\Domain\ValueObject;

/**
 * cahier des charges §8.1 : disponibilité (immédiate, date précise,
 * en poste - à l'écoute). La date n'a de sens que pour ScheduledDate —
 * le VO garantit qu'on ne peut pas construire un état incohérent.
 */
final readonly class Availability
{
    private function __construct(
        public AvailabilityStatus $status,
        public ?\DateTimeImmutable $availableFrom,
    ) {
        if (AvailabilityStatus::ScheduledDate === $status && null === $availableFrom) {
            throw new \InvalidArgumentException('Une date est requise pour une disponibilité à date précise.');
        }

        if (AvailabilityStatus::ScheduledDate !== $status && null !== $availableFrom) {
            throw new \InvalidArgumentException('La date ne s\'applique qu\'à une disponibilité à date précise.');
        }
    }

    public static function immediate(): self
    {
        return new self(AvailabilityStatus::Immediate, null);
    }

    public static function scheduledAt(\DateTimeImmutable $date): self
    {
        return new self(AvailabilityStatus::ScheduledDate, $date);
    }

    public static function employedOpenToOffers(): self
    {
        return new self(AvailabilityStatus::EmployedOpenToOffers, null);
    }
}
