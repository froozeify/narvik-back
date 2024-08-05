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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'install:default-settings', description: 'Generate default settings')]
class InstallDefaultSettings extends Command {
  private SymfonyStyle $io;

  public function __construct(
    private readonly GlobalSettingService $globalSettingService,
  ) {
    parent::__construct();
  }


  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->io = new SymfonyStyle($input, $output);

    $this->io->section("Génération des settings globaux par défaut");

    if (!$this->globalSettingService->getSettingValue(GlobalSetting::CONTROL_SHOOTING_ACTIVITY_ID)) {
      $this->io->writeln("Activité correspondante au tir contrôlé");
      $this->globalSettingService->updateSettingValue(GlobalSetting::CONTROL_SHOOTING_ACTIVITY_ID, null);
    }

    if (!$this->globalSettingService->getSettingValue(GlobalSetting::IGNORED_ACTIVITIES_OPENING_STATS)) {
      $this->io->writeln("Activités exclus du compte des jours ouverts");
      $this->globalSettingService->updateSettingValue(GlobalSetting::IGNORED_ACTIVITIES_OPENING_STATS, null);
    }

    if (!$this->globalSettingService->getSettingValue(GlobalSetting::LAST_ITAC_IMPORT)) {
      $this->io->writeln("Date du dernier import depuis itac");
      $this->globalSettingService->updateSettingValue(GlobalSetting::LAST_ITAC_IMPORT, null);
    }

    if (!$this->globalSettingService->getSettingValue(GlobalSetting::LAST_SECONDARY_CLUB_ITAC_IMPORT)) {
      $this->io->writeln("Date du dernier import club secondaire depuis itac");
      $this->globalSettingService->updateSettingValue(GlobalSetting::LAST_SECONDARY_CLUB_ITAC_IMPORT, null);
    }

    return Command::SUCCESS;
  }

}
