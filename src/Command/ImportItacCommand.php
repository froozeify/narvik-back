<?php

namespace App\Command;

use App\Service\ImportItacCsvService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
  name: 'import:itac',
  description: 'Import des membres depuis un CSV généré depuis itac.pro',
)]
class ImportItacCommand extends Command {
  public function __construct(
    private Filesystem $fs,
    private ImportItacCsvService $itacCsvService,
  ) {
    parent::__construct();
  }

  protected function configure(): void {
    $this->addArgument('file', InputArgument::REQUIRED, 'CSV Itac');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $io = new SymfonyStyle($input, $output);
    $filePath = $input->getArgument('file');

    if (!$this->fs->exists($filePath)) {
      $io->error("Fichier non trouvé");
      return Command::INVALID;
    }

    $response = $this->itacCsvService->importFromFile($filePath);
    $io->success("$response lignes de membres vont êtres importés");
    return Command::SUCCESS;
  }
}
