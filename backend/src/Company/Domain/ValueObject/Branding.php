<?php

declare(strict_types=1);

namespace App\Company\Domain\ValueObject;

/**
 * cahier des charges §5.2.2 : logo, couleurs, description, secteur
 * d'activité — stocké en jsonb, exposé ici comme un seul objet immuable.
 */
final readonly class Branding
{
    public function __construct(
        public ?string $logoUrl = null,
        public ?string $accentColor = null,
        public ?string $description = null,
        public ?string $sector = null,
    ) {}

    public static function empty(): self
    {
        return new self();
    }

    /**
     * @param array{logoUrl?: ?string, accentColor?: ?string, description?: ?string, sector?: ?string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            logoUrl: $data['logoUrl'] ?? null,
            accentColor: $data['accentColor'] ?? null,
            description: $data['description'] ?? null,
            sector: $data['sector'] ?? null,
        );
    }

    /**
     * @return array{logoUrl: ?string, accentColor: ?string, description: ?string, sector: ?string}
     */
    public function toArray(): array
    {
        return [
            'logoUrl' => $this->logoUrl,
            'accentColor' => $this->accentColor,
            'description' => $this->description,
            'sector' => $this->sector,
        ];
    }

    public function withSector(?string $sector): self
    {
        return new self($this->logoUrl, $this->accentColor, $this->description, $sector);
    }
}
