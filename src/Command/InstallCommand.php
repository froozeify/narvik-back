<?php

namespace App\Command;

use App\Entity\Member;
use App\Enum\GlobalSetting;
use App\Enum\MemberRole;
use App\Repository\MemberRepository;
use App\Service\GlobalSettingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsCommand(name: 'install', description: 'Create the bare default environment',)]
class InstallCommand extends Command {
  private SymfonyStyle $io;

  public function __construct(
    private EntityManagerInterface $em,
    private MemberRepository $memberRepository,
    private GlobalSettingService $globalSettingService,
    private UrlGeneratorInterface $router,
    private KernelInterface $kernel,
    private Filesystem $fs,
  ) {
    parent::__construct();
  }

  protected function configure(): void {

  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->io = new SymfonyStyle($input, $output);

    $this->creatingDatabaseSchema();
    $this->generateJwtKeys();
    $this->createAdminAccount();
    $this->createBadgerAccount();
    $this->generateBadgerLoginToken();
    $this->generateGlobalSettingsDefault();

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

  private function generateJwtKeys(): void {
    $this->io->section("Génération des clés JWT");

    $path = $this->kernel->getProjectDir() . "/config/jwt";
    $existingJwt = $this->fs->exists(["$path/private.pem", "$path/public.pem"]);
    if ($existingJwt) {
      $this->io->warning("\nClés JWT présentes, re-générer celles-ci va invalider toutes connexions actuels");
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
    }

    $jwtKeyGenerateInput = new ArrayInput([
      'command' => 'lexik:jwt:generate-keypair',
      '--overwrite' => true
    ]);
    $this->getApplication()->doRun($jwtKeyGenerateInput, $this->io);
  }


  private function createAdminAccount(): void {
    $this->io->section("Création d'un compte administrateur");

    $adminEmail = $this->io->askQuestion(new Question("Adresse mail administrateur", "admin@admin.com"));

    $dbAmin = $this->memberRepository->findOneByEmail($adminEmail);

    if ($dbAmin) {
      $this->io->info("Email déjà existant, création du compte administrateur ignoré");
      return;
    }

    $pwdQuestion = new Question("Mot de passe");
    $pwdQuestion->setHidden(true);
    $pwdQuestion->setValidator(function (?string $value): string {
      if (empty($value)) {
        throw new \Exception("Mot de passe invalide");
      }
      return $value;
    });

    $pwd = $this->io->askQuestion($pwdQuestion);
    $adminMember = new Member();
    $adminMember->setFirstname("admin")
      ->setLastname("admin")
      ->setEmail($adminEmail)
      ->setPlainPassword($pwd)
      ->setRole(MemberRole::admin)
      ->setAccountActivated(true);

    $this->em->persist($adminMember);
    $this->em->flush();
  }

  private function createBadgerAccount(): void {
    $this->io->section("Création du compte 'Badger'");
    $badger = $this->memberRepository->findOneByEmail('badger');

    if ($badger) {
      $this->io->info("Compte 'Badger' déjà présent");
      return;
    }

    $badgerMember = new Member();
    $badgerMember->setFirstname("badger")
      ->setLastname("badger")
      ->setEmail("badger")
      ->setPlainPassword("badger")
      ->setRole(MemberRole::badger);
    $this->em->persist($badgerMember);
    $this->em->flush();
  }

  private function generateBadgerLoginToken(): void {
    $this->io->section("Génération du token de connexion pour 'Badger'");

    $existingToken = false;
    if (!empty($this->globalSettingService->getSettingValue(GlobalSetting::BADGER_TOKEN))) {
      $existingToken = true;
    }

    if ($existingToken) {
      $question = new Question("Voulez-vous générer un nouveau token de connexion pour 'Badger' ? (oui/non)", "n");
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
    }

    $badgerToken = $this->randomToken(mt_rand(180, 200));
    $this->globalSettingService->updateSettingValue(GlobalSetting::BADGER_TOKEN, $badgerToken);

    $this->io->table([
      'token',
    ], [
      [
        $badgerToken,
      ]
    ]);
  }

  private function generateGlobalSettingsDefault(): void {
    $this->io->section("Génération des settings globaux par défaut");

    if (!$this->globalSettingService->getSettingValue(GlobalSetting::CONTROL_SHOOTING_ACTIVITY_ID)) {
      $this->io->writeln("Activité correspondante au tir contrôlé");
      $this->globalSettingService->updateSettingValue(GlobalSetting::CONTROL_SHOOTING_ACTIVITY_ID, null);
    }

    if (!$this->globalSettingService->getSettingValue(GlobalSetting::LAST_ITAC_IMPORT)) {
      $this->io->writeln("Date du dernier import depuis itac");
      $this->globalSettingService->updateSettingValue(GlobalSetting::LAST_ITAC_IMPORT, null);
    }
  }

  private function randomToken(int $length): string {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-';
    $charLength = strlen($characters) - 1;
    $result = '';
    for ($i = 0; $i < $length; $i++) {
      $result .= $characters[mt_rand(0, $charLength)];
    }
    return $result;
  }
}
