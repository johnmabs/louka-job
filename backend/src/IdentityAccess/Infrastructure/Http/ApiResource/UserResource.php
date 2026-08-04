<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Http\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\IdentityAccess\Infrastructure\Http\Processor\RegisterUserProcessor;
use App\IdentityAccess\Infrastructure\Http\Processor\VerifyEmailProcessor;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contrat HTTP de l'inscription. Découplé de l'entité Doctrine User :
 * ce fichier peut évoluer (nouveaux champs exposés, versionning API) sans
 * jamais toucher au Domain.
 */
#[ApiResource(
    shortName: 'User',
    operations: [
        new Post(
            uriTemplate: '/users',
            processor: RegisterUserProcessor::class,
        ),
        new Post(
            uriTemplate: '/users/{id}/verify_email',
            uriVariables: ['id'],
            status: 200,
            input: VerifyEmailInput::class,
            processor: VerifyEmailProcessor::class,
        ),
    ],
)]
final class UserResource
{
    #[ApiProperty(identifier: true, writable: false)]
    public ?string $id = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    #[ApiProperty(readable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 12)]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
        message: 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
    )]
    public string $plainPassword = '';

    #[ApiProperty(writable: false)]
    public ?string $status = null;
}
