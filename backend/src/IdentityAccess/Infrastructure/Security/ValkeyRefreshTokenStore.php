<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Security;

use App\IdentityAccess\Domain\Exception\InvalidRefreshTokenException;
use App\IdentityAccess\Domain\Exception\RefreshTokenReuseDetectedException;
use App\IdentityAccess\Domain\Security\RefreshTokenRotationResult;
use App\IdentityAccess\Domain\Security\RefreshTokenStoreInterface;
use App\IdentityAccess\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Uuid;

/**
 * Adapter Valkey du Port RefreshTokenStoreInterface.
 *
 * Modèle de clés :
 *  - token:{hash}         hash Redis : userId, chainId, deviceFingerprint, rotated
 *  - chain:{chainId}      set des hash de tous les tokens émis dans la chaîne
 *  - user_chains:{userId} set des chainId actifs d'un utilisateur
 *
 * "Usage unique" : un token déjà marqué rotated=1 qui est rejoué déclenche
 * la révocation de toute la chaîne (cf. cahier des charges §3.3).
 */
final class ValkeyRefreshTokenStore implements RefreshTokenStoreInterface
{
    private const int TTL_SECONDS = 30 * 86_400; // 30 jours

    public function __construct(
        private readonly \Redis $redis,
    ) {}

    public function issue(UserId $userId, string $deviceFingerprint): string
    {
        $chainId = Uuid::generate()->toString();

        return $this->issueWithinChain($userId, $chainId, $deviceFingerprint);
    }

    public function rotate(string $plainToken, string $deviceFingerprint): RefreshTokenRotationResult
    {
        $hash = $this->hash($plainToken);
        $record = $this->redis->hGetAll("token:{$hash}");

        if ([] === $record) {
            throw new InvalidRefreshTokenException('Refresh token inconnu ou expiré.');
        }

        $chainId = $record['chainId'];
        $userId = UserId::fromString($record['userId']);

        if ('1' === $record['rotated']) {
            // Ce token a déjà servi une fois : on est en train de le rejouer.
            $this->revokeChain($chainId);

            throw new RefreshTokenReuseDetectedException(
                'Ce refresh token a déjà été utilisé — toute la chaîne de sessions a été révoquée.',
            );
        }

        $this->redis->hSet("token:{$hash}", 'rotated', '1');

        $newPlainToken = $this->issueWithinChain($userId, $chainId, $deviceFingerprint);

        return new RefreshTokenRotationResult($userId, $newPlainToken);
    }

    public function revokeAllForUser(UserId $userId): void
    {
        $chains = $this->redis->sMembers("user_chains:{$userId->toString()}");

        foreach ($chains as $chainId) {
            $this->revokeChain($chainId);
        }

        $this->redis->del("user_chains:{$userId->toString()}");
    }

    private function issueWithinChain(UserId $userId, string $chainId, string $deviceFingerprint): string
    {
        $secret = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $plainToken = "{$chainId}.{$secret}";
        $hash = $this->hash($plainToken);

        $this->redis->hMSet("token:{$hash}", [
            'userId' => $userId->toString(),
            'chainId' => $chainId,
            'deviceFingerprint' => $deviceFingerprint,
            'rotated' => '0',
        ]);
        $this->redis->expire("token:{$hash}", self::TTL_SECONDS);

        $this->redis->sAdd("chain:{$chainId}", $hash);
        $this->redis->expire("chain:{$chainId}", self::TTL_SECONDS);

        $this->redis->sAdd("user_chains:{$userId->toString()}", $chainId);
        $this->redis->expire("user_chains:{$userId->toString()}", self::TTL_SECONDS);

        return $plainToken;
    }

    private function revokeChain(string $chainId): void
    {
        $hashes = $this->redis->sMembers("chain:{$chainId}");

        if ([] !== $hashes) {
            $keys = array_map(static fn(string $h): string => "token:{$h}", $hashes);
            $this->redis->del($keys);
        }

        $this->redis->del("chain:{$chainId}");
    }

    private function hash(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }
}
