<?php

declare(strict_types=1);

namespace App\CandidateProfile\Domain\Model;

/**
 * cahier des charges §8.2.
 */
enum ProfileVisibility: string
{
    case Private = 'private';
    case VisibleToRecruiters = 'visible_to_recruiters';
    case PublicProfile = 'public';
}
