<?php

declare(strict_types=1);

namespace App\CandidateProfile\Domain\Model;

use App\CandidateProfile\Domain\ValueObject\Availability;
use App\CandidateProfile\Domain\ValueObject\CandidateProfileId;
use App\CandidateProfile\Domain\ValueObject\Location;
use App\CandidateProfile\Domain\ValueObject\UserId;

/**
 * Racine d'agrégat du Bounded Context CandidateProfile.
 * Périmètre de cette itération : profil de base uniquement (headline,
 * résumé, localisation, disponibilité, visibilité) — expériences,
 * formations, compétences et CV viendront dans une itération ultérieure.
 */
final class CandidateProfile
{
    private CandidateProfileId $id;
    private UserId $userId;
    private ?string $headline;
    private ?string $summary;
    private ?Location $location;
    private ?Availability $availability;
    private ProfileVisibility $visibility;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    private function __construct(
        CandidateProfileId $id,
        UserId $userId,
        ?string $headline,
        ?string $summary,
        ?Location $location,
        ?Availability $availability,
        ProfileVisibility $visibility,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->headline = $headline;
        $this->summary = $summary;
        $this->location = $location;
        $this->availability = $availability;
        $this->visibility = $visibility;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * cahier des charges §8.1/UC2 : le profil démarre vide (privé par
     * défaut) — le candidat le complète progressivement, rien n'est
     * obligatoire à la création.
     */
    public static function createFor(UserId $userId): self
    {
        $now = new \DateTimeImmutable();

        return new self(
            id: CandidateProfileId::generate(),
            userId: $userId,
            headline: null,
            summary: null,
            location: null,
            availability: null,
            visibility: ProfileVisibility::Private,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public static function reconstitute(
        CandidateProfileId $id,
        UserId $userId,
        ?string $headline,
        ?string $summary,
        ?Location $location,
        ?Availability $availability,
        ProfileVisibility $visibility,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self($id, $userId, $headline, $summary, $location, $availability, $visibility, $createdAt, $updatedAt);
    }

    public function updateIdentity(?string $headline, ?string $summary): void
    {
        $this->headline = $headline;
        $this->summary = $summary;
        $this->touch();
    }

    public function updateLocation(?Location $location): void
    {
        $this->location = $location;
        $this->touch();
    }

    public function updateAvailability(?Availability $availability): void
    {
        $this->availability = $availability;
        $this->touch();
    }

    /**
     * cahier des charges §8.2, UC7 : action métier distincte, pas juste un
     * champ parmi d'autres — le candidat "gère la visibilité" explicitement.
     */
    public function changeVisibility(ProfileVisibility $visibility): void
    {
        $this->visibility = $visibility;
        $this->touch();
    }

    public function id(): CandidateProfileId
    {
        return $this->id;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function headline(): ?string
    {
        return $this->headline;
    }

    public function summary(): ?string
    {
        return $this->summary;
    }

    public function location(): ?Location
    {
        return $this->location;
    }

    public function availability(): ?Availability
    {
        return $this->availability;
    }

    public function visibility(): ProfileVisibility
    {
        return $this->visibility;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
