<?php

declare(strict_types=1);

namespace App\CandidateProfile\Domain\ValueObject;

enum AvailabilityStatus: string
{
    case Immediate = 'immediate';
    case ScheduledDate = 'date_precise';
    case EmployedOpenToOffers = 'en_poste_a_lecoute';
}
