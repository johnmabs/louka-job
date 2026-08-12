<?php

declare(strict_types=1);

namespace App\CandidateProfile\Application\Command\SaveProfile;

use App\CandidateProfile\Domain\Model\CandidateProfile;
use App\CandidateProfile\Domain\Repository\CandidateProfileRepositoryInterface;
use App\CandidateProfile\Domain\ValueObject\Availability;
use App\CandidateProfile\Domain\ValueObject\AvailabilityStatus;
use App\CandidateProfile\Domain\ValueObject\Location;
use App\CandidateProfile\Domain\ValueObject\RemotePreference;
use App\CandidateProfile\Domain\ValueObject\UserId;

final readonly class SaveCandidateProfileHandler
{
    public function __construct(
        private CandidateProfileRepositoryInterface $profiles,
    ) {}

    public function __invoke(SaveCandidateProfileCommand $command): CandidateProfile
    {
        $userId = UserId::fromString($command->userId);
        $profile = $this->profiles->ofUserId($userId) ?? CandidateProfile::createFor($userId);

        $profile->updateIdentity($command->headline, $command->summary);
        $profile->updateLocation($this->buildLocation($command));
        $profile->updateAvailability($this->buildAvailability($command));

        $this->profiles->save($profile);

        return $profile;
    }

    private function buildLocation(SaveCandidateProfileCommand $command): ?Location
    {
        if (null === $command->city) {
            return null;
        }

        return new Location(
            city: $command->city,
            mobilityRadiusKm: $command->mobilityRadiusKm,
            remotePreference: RemotePreference::from($command->remotePreference ?? RemotePreference::None->value),
        );
    }

    private function buildAvailability(SaveCandidateProfileCommand $command): ?Availability
    {
        if (null === $command->availabilityStatus) {
            return null;
        }

        $status = AvailabilityStatus::from($command->availabilityStatus);

        return match ($status) {
            AvailabilityStatus::Immediate => Availability::immediate(),
            AvailabilityStatus::EmployedOpenToOffers => Availability::employedOpenToOffers(),
            AvailabilityStatus::ScheduledDate => Availability::scheduledAt(
                new \DateTimeImmutable($command->availableFrom ?? 'now'),
            ),
        };
    }
}
