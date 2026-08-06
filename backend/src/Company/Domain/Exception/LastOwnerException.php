<?php

declare(strict_types=1);

namespace App\Company\Domain\Exception;

/**
 * Empêche de retirer ou rétrograder le dernier owner d'une Company —
 * une entreprise doit toujours avoir au moins un owner.
 */
final class LastOwnerException extends \DomainException {}
