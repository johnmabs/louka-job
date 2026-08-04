<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Console;

use App\IdentityAccess\Domain\Security\EmailVerificationTokenizerInterface;
use App\IdentityAccess\Domain\ValueObject\UserId;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Commande de développement uniquement — génère un token de vérification
 * pour un userId donné, sans passer par l'envoi d'email réel.
 * Utile pour tester le circuit API avant que le Mailer soit branché.
 */
#[AsCommand(
    name: 'app:identity-access:generate-verification-token',
    description: 'Génère un token de vérification email pour un utilisateur (dev uniquement)',
)]
final class GenerateVerificationTokenCommand extends Command
{
    public function __construct(
        private readonly EmailVerificationTokenizerInterface $tokenizer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('userId', InputArgument::REQUIRED, 'UUID du User');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = UserId::fromString((string) $input->getArgument('userId'));
        $token = $this->tokenizer->generateFor($userId);

        $output->writeln($token);

        return Command::SUCCESS;
    }
}
