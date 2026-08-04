<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Model;

use App\Shared\Domain\ValueObject\Email;
use App\IdentityAccess\Domain\ValueObject\UserId;
use App\IdentityAccess\Domain\Exception\EmailAlreadyVerifiedException;

/**
 * Racine d'agrégat (Aggregate Root) du Bounded Context Identity & Access.
 *
 * Aucune dépendance à Doctrine, Symfony HTTP, ou API Platform : ce fichier
 * doit pouvoir être testé unitairement sans aucune infrastructure démarrée.
 */
final class User
{
    private UserId $id;
    private Email $email;
    private string $passwordHash;
    private UserStatus $status;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $emailVerifiedAt;

    private function __construct(
        UserId $id,
        Email $email,
        string $passwordHash,
        UserStatus $status,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $emailVerifiedAt,
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->emailVerifiedAt = $emailVerifiedAt;
    }

    /**
     * Factory nommée : seul point d'entrée pour créer un nouveau compte.
     * Le constructeur privé empêche de contourner cette règle.
     */
    public static function register(Email $email, string $passwordHash): self
    {
        return new self(
            id: UserId::generate(),
            email: $email,
            passwordHash: $passwordHash,
            status: UserStatus::PendingVerification,
            createdAt: new \DateTimeImmutable(),
            emailVerifiedAt: null,
        );
    }

    /**
     * Reconstruction depuis la persistance (utilisée par le repository Doctrine).
     */
    public static function reconstitute(
        UserId $id,
        Email $email,
        string $passwordHash,
        UserStatus $status,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $emailVerifiedAt,
    ): self {
        return new self($id, $email, $passwordHash, $status, $createdAt, $emailVerifiedAt);
    }

    /**
     * Confirme l'email suite au clic sur le lien signé reçu par le candidat.
     * Idempotent en pratique : rejouer un lien déjà utilisé échoue ici,
     * car le statut n'est plus PendingVerification.
     */
    public function verifyEmail(\DateTimeImmutable $now): void
    {
        if ($this->status !== UserStatus::PendingVerification) {
            throw new EmailAlreadyVerifiedException('Ce compte est déjà vérifié ou dans un état incompatible.');
        }

        $this->status = UserStatus::Active;
        $this->emailVerifiedAt = $now;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function status(): UserStatus
    {
        return $this->status;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function emailVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }
}
