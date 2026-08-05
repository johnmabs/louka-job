<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Exception;

/**
 * Levée quand un refresh token déjà utilisé est rejoué : signe probable de
 * vol de token. La chaîne entière est révoquée avant que cette exception
 * ne soit levée (cf. cahier des charges §3.3).
 */
final class RefreshTokenReuseDetectedException extends \DomainException {}
