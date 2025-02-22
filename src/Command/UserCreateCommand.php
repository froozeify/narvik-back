<?php

namespace App\Command;

use App\Entity\User;
use App\Enum\UserRole;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'user:create', description: 'Create an user')]
class UserCreateCommand extends Command {
  private SymfonyStyle $io;

  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly UserRepository $userRepository,
    private readonly ValidatorInterface $validator,
  ) {
    parent::__construct();
  }

  protected function configure(): void {
    $this->addOption('email', null,InputOption::VALUE_OPTIONAL, 'Email');
    $this->addOption('password', null,InputOption::VALUE_OPTIONAL, 'Mot de passe');
    $this->addOption('firstname', null,InputOption::VALUE_OPTIONAL, 'Prénom');
    $this->addOption('lastname', null,InputOption::VALUE_OPTIONAL, 'Nom');
    $this->addOption('role', null,InputOption::VALUE_OPTIONAL, 'Rôle. Valeurs possibles : ' . implode(', ', array_column(UserRole::cases(), 'value')));
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->io = new SymfonyStyle($input, $output);

    // Field are not defined
    $email = $input->getOption('email');
    if (!$email) {
      $email = $this->io->askQuestion(new Question("Adresse mail", "admin@admin.com"));
    }

    $dbMember = $this->userRepository->findOneByEmail($email);
    if ($dbMember) {
      $this->io->info("Email déjà existant, création du compte ignoré.");
      return Command::INVALID;
    }

    $password = $input->getOption('password');
    if (!$password) {
      $pwdQuestion = new Question("Mot de passe");
      $pwdQuestion->setHidden(true);
      $pwdQuestion->setValidator(function (?string $value): string {
        if (empty($value)) {
          throw new \Exception("Mot de passe invalide");
        }
        return $value;
      });

      $password = $this->io->askQuestion($pwdQuestion);
    }

    $firstname = $input->getOption('firstname');
    if (!$firstname) {
      $question = new Question("Prénom");
      $question->setValidator(function (?string $value): string {
        if (empty($value)) {
          throw new \Exception("Champ requis");
        }
        return $value;
      });
      $firstname = $this->io->askQuestion($question);
    }

    $lastname = $input->getOption('lastname');
    if (!$lastname) {
      $question = new Question("Nom");
      $question->setValidator(function (?string $value): string {
        if (empty($value)) {
          throw new \Exception("Champ requis");
        }
        return $value;
      });
      $lastname = $this->io->askQuestion($question);
    }

    $role = $input->getOption('role');
    if (!$role) {
      $role = $this->io->askQuestion(new Question("Rôle. Valeurs possibles : " . implode(', ', array_column(UserRole::cases(), 'value')), UserRole::super_admin->value));
    }
    $role = UserRole::tryFrom($role) ?? UserRole::super_admin;

    $this->createAccount($email, $password, $firstname, $lastname, $role);

    return Command::SUCCESS;
  }

  private function createAccount(string $email, string $password, string $firstname, string $lastname, UserRole $role): void {
    $user = new User();
    $user
      ->setFirstname($firstname)
      ->setLastname($lastname)
      ->setEmail($email)
      ->setPlainPassword($password)
      ->setRole($role)
      ->setAccountActivated(true);

    $errors = $this->validator->validate($user);
    if (count($errors) > 0) {
      $this->io->error('Erreur lors la création du compte');
      $this->io->error((string) $errors);
      return;
    }

    $this->em->persist($user);
    $this->em->flush();

    $this->io->success('Compte créé.');
  }

}
