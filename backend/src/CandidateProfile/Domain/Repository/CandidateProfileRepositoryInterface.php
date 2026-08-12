<?php

declare(strict_types=1);

namespace App\CandidateProfile\Domain\Repository;

use App\CandidateProfile\Domain\Model\CandidateProfile;
use App\CandidateProfile\Domain\ValueObject\CandidateProfileId;
use App\CandidateProfile\Domain\ValueObject\UserId;

interface CandidateProfileRepositoryInterface
{
    public function save(CandidateProfile $profile): void;
    public function ofId(CandidateProfileId $id): ?CandidateProfile;
    public function ofUserId(UserId $userId): ?CandidateProfile;
}
