<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Http\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Company\Infrastructure\Http\Processor\CreateCompanyProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Company',
    operations: [
        new Post(
            uriTemplate: '/companies',
            processor: CreateCompanyProcessor::class,
        ),
    ],
)]
final class CompanyResource
{
    #[ApiProperty(identifier: true, writable: false)]
    public ?string $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    public string $name = '';

    #[Assert\Length(exactly: 14, exactMessage: 'Le SIRET doit comporter exactement 14 chiffres.')]
    #[Assert\Regex(pattern: '/^\d{14}$/', message: 'Le SIRET doit être composé de 14 chiffres.')]
    public ?string $siret = null;

    #[ApiProperty(writable: false)]
    public ?string $slug = null;

    #[ApiProperty(writable: false)]
    public ?string $verificationStatus = null;
}
