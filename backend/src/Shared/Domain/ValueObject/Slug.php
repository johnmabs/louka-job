<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidSlugException;

/**
 * Value Object générique : identifiant textuel unique et lisible utilisé
 * dans les URLs (cf. glossaire cahier des charges §2). Utilisé par Company
 * et, plus tard, JobOffer.
 */
final readonly class Slug
{
    private const string PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    private string $value;

    private function __construct(string $value)
    {
        if (1 !== preg_match(self::PATTERN, $value)) {
            throw new InvalidSlugException(sprintf('"%s" n\'est pas un slug valide.', $value));
        }

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Génère un slug à partir d'un texte libre (ex. nom d'entreprise, titre
     * d'offre). $suffix optionnel pour l'anti-collision (ex. hash court).
     */
    public static function generate(string $source, ?string $suffix = null): self
    {
        $normalized = self::slugify($source);

        if (null !== $suffix) {
            $normalized .= '-' . self::slugify($suffix);
        }

        return new self($normalized);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function slugify(string $text): string
    {
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
        $lower = mb_strtolower($transliterated);
        $dashed = preg_replace('/[^a-z0-9]+/', '-', $lower) ?? '';

        return trim($dashed, '-');
    }
}
