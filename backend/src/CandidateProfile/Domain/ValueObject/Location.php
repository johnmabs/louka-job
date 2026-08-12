<?php

declare(strict_types=1);

namespace App\CandidateProfile\Domain\ValueObject;

/**
 * cahier des charges §8.1 : ville, rayon de mobilité, télétravail souhaité.
 */
final readonly class Location
{
    public function __construct(
        public string $city,
        public ?int $mobilityRadiusKm,
        public RemotePreference $remotePreference,
    ) {
        if ('' === trim($city)) {
            throw new \InvalidArgumentException('La ville est obligatoire.');
        }

        if (null !== $mobilityRadiusKm && $mobilityRadiusKm < 0) {
            throw new \InvalidArgumentException('Le rayon de mobilité ne peut pas être négatif.');
        }
    }

    /**
     * @param array{city: string, mobilityRadiusKm: ?int, remotePreference: string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            city: $data['city'],
            mobilityRadiusKm: $data['mobilityRadiusKm'],
            remotePreference: RemotePreference::from($data['remotePreference']),
        );
    }

    /**
     * @return array{city: string, mobilityRadiusKm: ?int, remotePreference: string}
     */
    public function toArray(): array
    {
        return [
            'city' => $this->city,
            'mobilityRadiusKm' => $this->mobilityRadiusKm,
            'remotePreference' => $this->remotePreference->value,
        ];
    }
}
