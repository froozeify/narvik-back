<?php

namespace App\Command;

use App\Repository\UserSecurityCodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'clean', description: 'Remove expired data from the app (database & storage)')]
class CleanupCommand extends Command {
  private SymfonyStyle $io;

  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly UserSecurityCodeRepository $memberSecurityCodeRepository,
  ) {
    parent::__construct();
  }


  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->io = new SymfonyStyle($input, $output);

    $this->cleanJwt();
    $this->cleanSecurityCodes();

    return Command::SUCCESS;
  }

  private function cleanJwt(): void {
    $this->io->section("Refresh token");

    $command = new ArrayInput([
      'command' => 'gesdinet:jwt:clear',
    ]);
    $this->getApplication()->doRun($command, $this->io);
  }

  private function cleanSecurityCodes(): void {
    $this->io->section("Removing expired security codes");
    $oldSecurityCodes = $this->memberSecurityCodeRepository->findExpired();
    foreach ($oldSecurityCodes as $securityCode) {
      $this->io->writeln("{$securityCode->getId()}");

      $this->entityManager->remove($securityCode);
    }

    $this->entityManager->flush();
  }

}
