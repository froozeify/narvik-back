<?php

namespace App\Command;

use App\Service\ImportCerbereService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
  name: 'import:cerbere:presences',
  description: 'Importe toute les présences enregistrées dans Cerbère',
)]
class ImportCerberePresencesCommand extends Command {

//  private array $csvActivities = [];

  public function __construct(
    private Filesystem $fs,
    private ImportCerbereService $importCerbereService,
//    private EntityManagerInterface $em,
//    private ActivityRepository $activityRepository,
//    private MessageBusInterface $bus,
  ) {
    parent::__construct();
  }

  protected function configure(): void {
    $this->addArgument('file', InputArgument::REQUIRED, 'Fichier XLS généré par cerbère');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $io = new SymfonyStyle($input, $output);
    $filePath = $input->getArgument('file');

    if (!$this->fs->exists($filePath)) {
      $io->error("Fichier non trouvé");
      return Command::INVALID;
    }

    $io->warning("\n
Avant d'exécuter cette commande, n'oubliez pas d'effectuer une importation de membres (et assurez-vous que l'importation est bien terminée).\n
Dans le cas contraire, certaines présences importées pourraient être liées à un tireur externe au lieu d'un membre.
    ");

    $question = new Question("Continuer l'import ? (oui/non)", "n");
    $question->setValidator(function (?string $value): string {
      if (empty($value)) {
        $value = "o";
      }
      return strtolower($value);
    });
    $question = $io->askQuestion($question);
    if ($question[0] !== "o") {
      return Command::SUCCESS;
    }

    $presences = $this->importCerbereService->importFromFile($filePath);
    $io->writeln($presences . " jours de présence à importer");
    return Command::SUCCESS;
  }
}
