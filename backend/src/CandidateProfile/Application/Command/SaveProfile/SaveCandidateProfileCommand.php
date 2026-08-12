<?php

declare(strict_types=1);

namespace App\CandidateProfile\Application\Command\SaveProfile;

final readonly class SaveCandidateProfileCommand
{
    public function __construct(
        public string $userId,
        public ?string $headline,
        public ?string $summary,
        public ?string $city,
        public ?int $mobilityRadiusKm,
        public ?string $remotePreference,
        public ?string $availabilityStatus,
        public ?string $availableFrom,
    ) {}
}
