<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Http\ApiResource;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateRecruiterInput
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 12)]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
        message: 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
    )]
    public string $plainPassword = '';

    /**
     * "owner" n'est jamais assignable ici — seul Company::register() peut
     * créer un owner, à la création de l'entreprise.
     */
    #[Assert\Choice(choices: ['admin', 'recruiter', 'viewer'])]
    public string $role = 'recruiter';
}
