<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Redis;

/**
 * Factory pour un client \Redis natif (extension PECL), qui parle le
 * protocole RESP — compatible Valkey sans aucune adaptation (cf. ADR-0002).
 */
final class RedisClientFactory
{
    public static function create(string $dsn): \Redis
    {
        $parts = parse_url($dsn);

        if (false === $parts || !isset($parts['host'])) {
            throw new \InvalidArgumentException(sprintf('DSN Redis/Valkey invalide : "%s".', $dsn));
        }

        $redis = new \Redis();
        $redis->connect($parts['host'], $parts['port'] ?? 6379);

        return $redis;
    }
}
