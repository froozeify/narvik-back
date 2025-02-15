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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsCommand(name: 'install', description: 'Create the bare default environment')]
class InstallCommand extends Command {
  private SymfonyStyle $io;

  public function __construct(
    private readonly ParameterBagInterface $params,
    private readonly EntityManagerInterface $em,
    private readonly ClientRepository $clientRepository,
    private readonly MemberRepository $memberRepository,
    private readonly GlobalSettingService $globalSettingService,
    private readonly UrlGeneratorInterface $router,
    private readonly KernelInterface $kernel,
    private readonly Filesystem $fs,
  ) {
    parent::__construct();
  }

  protected function configure(): void {

  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->io = new SymfonyStyle($input, $output);

    $this->creatingDatabaseSchema();
//    $this->generateJwtKeys();
    $this->createAdminAccount();
    $this->generateGlobalSettingsDefault();
    $this->generateClients();

    $this->io->success('Environnement configuré');
    return Command::SUCCESS;
  }

  private function creatingDatabaseSchema(): void {
    $this->io->section("Génération de la base données");

    $schemaManager = $this->em->getConnection()->createSchemaManager();
    $tables = $schemaManager->listTables();
    if (count($tables) > 0) {
      foreach ($tables as $table) {
        if ($table->getName() === "member") { // We got the member table, we know the db schema is present
          $this->io->info("Base de données déjà présente");
          return;
        }
      }
    }

    $doctrineSchemaUpdateInput = new ArrayInput([
      'command' => 'doctrine:schema:update',
      '--force' => true,
    ]);
    $this->getApplication()->doRun($doctrineSchemaUpdateInput, $this->io);
  }

//  private function generateJwtKeys(): void {
//    $this->io->section("Génération des clés JWT");
//
//    $path = $this->kernel->getProjectDir() . "/config/jwt";
//    $existingJwt = $this->fs->exists(["$path/private.pem", "$path/public.pem"]);
//    if ($existingJwt) {
//      $this->io->warning("\nClés JWT présentes, re-générer celles-ci va invalider toutes connexions actuels");
//      $question = new Question("Voulez-vous générer des nouvelles clés JWT ? (oui/non)", "n");
//      $question->setValidator(function (?string $value): string {
//        if (empty($value)) {
//          $value = "o";
//        }
//        return strtolower($value);
//      });
//      $question = $this->io->askQuestion($question);
//      if ($question[0] !== "o") {
//        return;
//      }
//    }
//
//    $jwtKeyGenerateInput = new ArrayInput([
//      'command' => 'lexik:jwt:generate-keypair',
//      '--overwrite' => true
//    ]);
//    $this->getApplication()->doRun($jwtKeyGenerateInput, $this->io);
//  }


  private function createAdminAccount(): void {
    $this->io->section("Création d'un compte administrateur");

    $command = new ArrayInput([
      'command' => 'user:create',
      '--role' => UserRole::super_admin->value,
      '--firstname' => 'Admin',
      '--lastname' => 'ADMIN',
    ]);
    $this->getApplication()->doRun($command, $this->io);
  }

  private function generateGlobalSettingsDefault(): void {
    $command = new ArrayInput([
      'command' => 'install:default-settings',
    ]);
    $this->getApplication()->doRun($command, $this->io);
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
