<?php

namespace App\Service;

use App\Entity\ClubDependent\Member;
use App\Entity\User;
use App\Entity\UserSecurityCode;
use App\Enum\ClubRole;
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

  public function initiateAccountValidation(User $user): bool {
    if ($user->isAccountActivated() || !$this->emailService->canSendEmail()) {
      return false;
    }

    // We verify that we don't have more than 4 in progress reset for this user
    $validationInProgress = $this->userSecurityCodeRepository->findAllByTrigger($user, UserSecurityCodeTrigger::accountValidation);
    if (count($validationInProgress) > 3) {
      return false;
    }

    $securityCode = new UserSecurityCode();
    $securityCode->setTrigger(UserSecurityCodeTrigger::accountValidation)->setUser($user);

    $this->em->persist($securityCode);
    $this->em->flush();

    // We send the security code
    $email = $this->emailService->getEmail('security-code.html.twig', 'Validation du compte', ['security_code' => $securityCode->getCode()]);
    $this->emailService->sendEmail($email, $user->getEmail());

    return true;
  }

  public function activateAccount(User $user): void {
    $user->setAccountActivated(true);
    $this->em->persist($user);
    $this->em->flush();
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

  public function createOrGetFromMember(Member $member): ?User {
    if (!$member->getEmail()) {
      return null;
    }

    $user = $this->userRepository->findOneByEmail($member->getEmail());
    if (!$user) {
      $user = new User();
      $user
        ->setEmail($member->getEmail())
        ->setFirstname($member->getFirstname())
        ->setLastname($member->getLastname())
        ->setAccountActivated(false);
      $this->em->persist($user);
      $this->em->flush();
    }

    return $user;
  }
}
