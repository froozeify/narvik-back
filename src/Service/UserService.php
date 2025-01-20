<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserSecurityCode;
use App\Enum\UserSecurityCodeTrigger;
use App\Mailer\EmailService;
use App\Repository\UserRepository;
use App\Repository\UserSecurityCodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService {
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly EmailService $emailService,
    private readonly UserPasswordHasherInterface $passwordHasher,
    private readonly UserRepository $userRepository,
    private readonly userSecurityCodeRepository $userSecurityCodeRepository,
  ) {
  }

  /**
   * @param User $user
   * @param string $password
   * @return string|null Error message or null if ever everything is ok
   */
  public function changeUserPassword(User $user, string $password, bool $flush = true): ?string {
    if (empty($password) || strlen($password) < 8) {
      return 'Password must be at least 8 letters long.';
    }

    $this->userRepository->upgradePassword($user, $this->passwordHasher->hashPassword($user, $password), $flush);

    return null;
  }

  public function initiateResetPassword(User $user): bool {
    if (!$user->isAccountActivated() || !$this->emailService->canSendEmail()) {
      return false;
    }

    // We verify that we don't have more than 4 in progress reset for this user
    $resetInProgress = $this->userSecurityCodeRepository->findAllByTrigger($user, UserSecurityCodeTrigger::resetPassword);
    if (count($resetInProgress) > 3) {
      return false;
    }

    $securityCode = new UserSecurityCode();
    $securityCode->setTrigger(UserSecurityCodeTrigger::resetPassword)->setUser($user);

    $this->em->persist($securityCode);
    $this->em->flush();

    // We sent the security code
    $email = $this->emailService->getEmail('security-code.html.twig', 'Changement de mot de passe', ['security_code' => $securityCode->getCode()]);
    $this->emailService->sendEmail($email, $user->getEmail());

    return true;
  }

  public function validateSecurityCode(User $user, UserSecurityCodeTrigger $trigger, string $securityCode): bool {
    $securityCodeQuery = $this->userSecurityCodeRepository->findLastOneForUser($user, $trigger);
    if (!$securityCodeQuery || $securityCodeQuery->getCode() !== trim($securityCode)) {
      return false;
    }

    // We consume all
    $codes = $this->userSecurityCodeRepository->findAllByTrigger($user, UserSecurityCodeTrigger::resetPassword);
    foreach ($codes as $code) {
      $this->em->remove($code);
    }
    $this->em->flush();

    return true;
  }
}
