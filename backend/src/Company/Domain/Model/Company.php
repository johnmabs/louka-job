<?php

declare(strict_types=1);

namespace App\Company\Domain\Model;

use App\Company\Domain\Exception\InsufficientCompanyPermissionException;
use App\Company\Domain\Exception\LastOwnerException;
use App\Company\Domain\Exception\MemberAlreadyExistsException;
use App\Company\Domain\Exception\MemberNotFoundException;
use App\Company\Domain\Model\CompanyMember;
use App\Company\Domain\Model\CompanyRole;
use App\Company\Domain\Model\VerificationStatus;
use App\Company\Domain\ValueObject\Branding;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Slug;

/**
 * Racine d'agrégat du Bounded Context Company. CompanyMember vit à
 * l'intérieur de cet agrégat : les invariants ("toujours au moins un
 * owner", "pas de doublon de membre") sont protégés ici, pas éparpillés.
 */
final class Company
{
    private CompanyId $id;
    private string $name;
    private Slug $slug;
    private ?string $siret;
    private Branding $branding;
    private VerificationStatus $verificationStatus;
    private \DateTimeImmutable $createdAt;

    /** @var list<CompanyMember> */
    private array $members;

    private function __construct(
        CompanyId $id,
        string $name,
        Slug $slug,
        ?string $siret,
        Branding $branding,
        VerificationStatus $verificationStatus,
        \DateTimeImmutable $createdAt,
        array $members,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->siret = $siret;
        $this->branding = $branding;
        $this->verificationStatus = $verificationStatus;
        $this->createdAt = $createdAt;
        $this->members = $members;
    }

    /**
     * cahier des charges §6.1 : le créateur devient automatiquement owner.
     */
    public static function register(string $name, Slug $slug, ?string $siret, UserId $ownerUserId): self
    {
        $company = new self(
            id: CompanyId::generate(),
            name: $name,
            slug: $slug,
            siret: $siret,
            branding: Branding::empty(),
            verificationStatus: VerificationStatus::Pending,
            createdAt: new \DateTimeImmutable(),
            members: [],
        );

        $company->members[] = new CompanyMember($ownerUserId, CompanyRole::Owner, new \DateTimeImmutable());

        return $company;
    }

    /**
     * @param list<CompanyMember> $members
     */
    public static function reconstitute(
        CompanyId $id,
        string $name,
        Slug $slug,
        ?string $siret,
        Branding $branding,
        VerificationStatus $verificationStatus,
        \DateTimeImmutable $createdAt,
        array $members,
    ): self {
        return new self($id, $name, $slug, $siret, $branding, $verificationStatus, $createdAt, $members);
    }

    /**
     * cahier des charges §7.3 : réservé à owner/admin.
     */
    public function inviteMember(UserId $newMemberId, CompanyRole $role, UserId $invitedBy): void
    {
        if ($this->hasMember($newMemberId)) {
            throw new MemberAlreadyExistsException('Cet utilisateur est déjà membre de cette entreprise.');
        }

        $this->requirePermission($invitedBy, fn(CompanyRole $r) => $r->canManageMembers());

        $this->members[] = new CompanyMember($newMemberId, $role, new \DateTimeImmutable());
    }

    public function removeMember(UserId $memberId, UserId $removedBy): void
    {
        $member = $this->memberOf($memberId);

        if (null === $member) {
            throw new MemberNotFoundException('Ce membre n\'existe pas dans cette entreprise.');
        }

        $this->requirePermission($removedBy, fn(CompanyRole $r) => $r->canManageMembers());

        if (CompanyRole::Owner === $member->role() && 1 === $this->countOwners()) {
            throw new LastOwnerException('Impossible de retirer le dernier owner de l\'entreprise.');
        }

        $this->members = array_values(array_filter(
            $this->members,
            static fn(CompanyMember $m): bool => !$m->userId()->equals($memberId),
        ));
    }

    public function changeMemberRole(UserId $memberId, CompanyRole $newRole, UserId $changedBy): void
    {
        $member = $this->memberOf($memberId);

        if (null === $member) {
            throw new MemberNotFoundException('Ce membre n\'existe pas dans cette entreprise.');
        }

        $this->requirePermission($changedBy, fn(CompanyRole $r) => $r->canManageMembers());

        if (CompanyRole::Owner === $member->role() && CompanyRole::Owner !== $newRole && 1 === $this->countOwners()) {
            throw new LastOwnerException('Impossible de rétrograder le dernier owner de l\'entreprise.');
        }

        $member->changeRole($newRole);
    }

    public function markVerified(): void
    {
        $this->verificationStatus = VerificationStatus::Verified;
    }

    public function markRejected(): void
    {
        $this->verificationStatus = VerificationStatus::Rejected;
    }

    public function id(): CompanyId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function slug(): Slug
    {
        return $this->slug;
    }

    public function siret(): ?string
    {
        return $this->siret;
    }

    public function branding(): Branding
    {
        return $this->branding;
    }

    public function verificationStatus(): VerificationStatus
    {
        return $this->verificationStatus;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return list<CompanyMember>
     */
    public function members(): array
    {
        return $this->members;
    }

    private function hasMember(UserId $userId): bool
    {
        return null !== $this->memberOf($userId);
    }

    private function memberOf(UserId $userId): ?CompanyMember
    {
        foreach ($this->members as $member) {
            if ($member->userId()->equals($userId)) {
                return $member;
            }
        }

        return null;
    }

    private function countOwners(): int
    {
        return count(array_filter(
            $this->members,
            static fn(CompanyMember $m): bool => CompanyRole::Owner === $m->role(),
        ));
    }

    /**
     * @param callable(CompanyRole): bool $predicate
     */
    private function requirePermission(UserId $actorId, callable $predicate): void
    {
        $actor = $this->memberOf($actorId);

        if (null === $actor || !$predicate($actor->role())) {
            throw new InsufficientCompanyPermissionException(
                'Cette action nécessite un rôle owner ou admin dans cette entreprise.',
            );
        }
    }
}
