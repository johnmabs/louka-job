<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

/**
 * Ajuste le payload du JWT généré par Lexik pour correspondre exactement
 * au contrat du cahier des charges §3.3 : sub (UUID), roles, iat,
 * company_id (null tant que le module Company n'existe pas).
 */
final class JwtPayloadEnricher
{
    public function __invoke(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof SecurityUser) {
            return;
        }

        $payload = $event->getData();
        unset($payload['username']);

        $payload['sub'] = $user->id()->toString();
        $payload['iat'] = time();
        $payload['company_id'] = null;

        $event->setData($payload);
    }
}
