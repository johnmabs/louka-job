<?php

declare(strict_types=1);

namespace App\CandidateProfile\Domain\ValueObject;

enum RemotePreference: string
{
    case None = 'non';
    case Hybrid = 'hybride';
    case Full = 'full';
}
