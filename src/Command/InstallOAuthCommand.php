<?php

namespace App\Command;

use App\Enum\UserRole;
use App\Repository\ClubDependent\MemberRepository;
use App\Service\GlobalSettingService;
use Doctrine\ORM\EntityManagerInterface;
use League\Bundle\OAuth2ServerBundle\Repository\ClientRepository;
use phpDocumentor\Reflection\PseudoTypes\IntegerRange;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsCommand(name: 'install:oauth', description: 'Install oauth and his minimum require client')]
class InstallOAuthCommand extends Command {
  private SymfonyStyle $io;

  public function __construct(
    private readonly ParameterBagInterface $params,
    private readonly ClientRepository $clientRepository,
    private readonly KernelInterface $kernel,
    private readonly Filesystem $fs,
  ) {
    parent::__construct();
  }

  protected function configure(): void {
    $this->addOption('force', 'f',InputOption::VALUE_NONE, 'Force regenerating the key');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->io = new SymfonyStyle($input, $output);

    $this->generateKeys($input->getOption('force'));
    $this->generateClients();

    $this->io->success('OAuth configuré');
    return Command::SUCCESS;
  }

  private function generateKeys(bool $force = false): void {
    $this->io->section("Génération des clés JWT");

    $path = $this->kernel->getProjectDir() . "/config/jwt";
    $existingJwt = $this->fs->exists(["$path/private.pem", "$path/public.pem"]);

    $oauthPassphrase = $_ENV['OAUTH_PASSPHRASE'];
    if (!$oauthPassphrase) {
      throw new \Exception("Env var 'OAUTH_PASSPHRASE' is not defined");
    }

    if ($existingJwt) {
      $this->io->info("Clés JWT présentes");

      if ($force) {
        $question = new Question("Voulez-vous générer des nouvelles clés JWT ? (oui/non)", "n");
        $question->setValidator(function (?string $value): string {
          if (empty($value)) {
            $value = "o";
          }
          return strtolower($value);
        });
        $question = $this->io->askQuestion($question);
        if ($question[0] !== "o") {
          return;
        }
      } else {
        return;
      }
    }

    $jwtKeyGenerateInput = new ArrayInput([
      'command' => 'league:oauth2-server:generate-keypair',
      '--overwrite' => true
    ]);
    $this->getApplication()->doRun($jwtKeyGenerateInput, $this->io);
  }


  private function generateClients(): void {
    $this->io->section("Création du client badger");
    if ($this->clientRepository->getClientEntity('badger')) {
      $this->io->info("Client déjà enregistré");
      return;
    }

    $secret = $this->params->get('league.oauth2_server.encryption_key');

    $command = new ArrayInput([
      'command' => 'league:oauth2-server:create-client',
      'name' => 'badger',
      'identifier' => 'badger',
      'secret' => $secret,
      '--scope' => ['badger']
    ]);
    $this->getApplication()->doRun($command, $this->io);
  }
}
