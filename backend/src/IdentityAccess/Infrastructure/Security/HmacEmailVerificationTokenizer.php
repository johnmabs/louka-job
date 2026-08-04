<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Security;

use App\IdentityAccess\Domain\Exception\InvalidVerificationTokenException;
use App\IdentityAccess\Domain\Security\EmailVerificationTokenizerInterface;
use App\IdentityAccess\Domain\ValueObject\UserId;

/**
 * Token auto-porteur signé HMAC-SHA256 : aucune table de tokens en base.
 * Format : base64url(userId|expiresAt).signature
 *
 * "Usage unique" garanti au niveau du Domain (User::verifyEmail() refuse
 * si le compte n'est plus "pending"), pas au niveau du token lui-même.
 */
final class HmacEmailVerificationTokenizer implements EmailVerificationTokenizerInterface
{
    private const int TTL_SECONDS = 86_400; // 24h, cf. cahier des charges §5.2

    public function __construct(
        #[\SensitiveParameter] private readonly string $secret,
    ) {}

    public function generateFor(UserId $userId): string
    {
        $payload = $userId->toString() . '|' . (time() + self::TTL_SECONDS);

        return $this->encode($payload) . '.' . $this->sign($payload);
    }

    public function verify(UserId $userId, string $token): void
    {
        $parts = explode('.', $token, 2);

        if (2 !== count($parts)) {
            throw new InvalidVerificationTokenException('Format de lien invalide.');
        }

        [$encodedPayload, $signature] = $parts;
        $payload = $this->decode($encodedPayload);

        if (null === $payload || !hash_equals($this->sign($payload), $signature)) {
            throw new InvalidVerificationTokenException('Signature de lien invalide.');
        }

        [$tokenUserId, $expiresAt] = explode('|', $payload, 2);

        if ($tokenUserId !== $userId->toString()) {
            throw new InvalidVerificationTokenException('Ce lien ne correspond pas à ce compte.');
        }

        if ((int) $expiresAt < time()) {
            throw new InvalidVerificationTokenException('Ce lien a expiré.');
        }
    }

    private function sign(string $payload): string
    {
        return $this->encode(hash_hmac('sha256', $payload, $this->secret, true));
    }

    private function encode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function decode(string $value): ?string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        return false === $decoded ? null : $decoded;
    }
}
