<?php

namespace App\Service;

use App\Entity\Member;
use App\Entity\MemberSecurityCode;
use App\Enum\GlobalSetting;
use App\Enum\MemberSecurityCodeTrigger;
use App\Mailer\EmailService;
use App\Repository\ActivityRepository;
use App\Repository\MemberPresenceRepository;
use App\Repository\MemberRepository;
use App\Repository\MemberSecurityCodeRepository;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberService {
  public function __construct(
    private readonly ImageService $imageService,
    private readonly GlobalSettingService $globalSettingService,
    private readonly EntityManagerInterface $em,
    private readonly EmailService $emailService,
    private readonly UserPasswordHasherInterface $passwordHasher,
    private readonly ActivityRepository $activityRepository,
    private readonly MemberRepository $memberRepository,
    private readonly MemberPresenceRepository $memberPresenceRepository,
    private readonly MemberSecurityCodeRepository $memberSecurityCodeRepository,
    private readonly SeasonRepository $seasonRepository,
  ) {
  }

  public function setProfileImage(Member $member): void {
    if ($member->getLicence() && $photoPath = $this->imageService->getMemberPhotoPath($member->getLicence())) {
      $member->setProfileImage($photoPath);
    }
  }

  public function setCurrentSeason(Member $member): void {
    $currentSeason = $this->seasonRepository->findCurrentSeason();
    if (!$currentSeason) return;

    foreach ($member->getMemberSeasons() as $season) {
      if ($season->getSeason() === $currentSeason) {
        $member->setCurrentSeason($season);
        break;
      }
    }
  }

  public function setLastControlShooting(Member $member, ?string $controlShootingActivity = null): void {
    if (!$controlShootingActivity) {
      $controlShootingActivity = $this->globalSettingService->getSettingValue(GlobalSetting::CONTROL_SHOOTING_ACTIVITY_ID);
    }

    // No control shooting defined
    if (!$controlShootingActivity || !is_numeric($controlShootingActivity)) {
      return;
    }

    $controlShootingActivity = $this->activityRepository->find($controlShootingActivity);
    if (!$controlShootingActivity) { // Control shooting id passed does not exist
      return;
    }

    $presence = $this->memberPresenceRepository->findLastOneByActivity($member, $controlShootingActivity);
    if ($presence) {
      $member->setLastControlShooting($presence->getDate());
    }
  }

  /**
   * @param Member $member
   * @param string $password
   * @return string|null Error message or null if ever everything is ok
   */
  public function changeMemberPassword(Member $member, string $password): ?string {
    if (empty($password) || strlen($password) < 8) {
      return 'Password must be at least 8 letters long';
    }

    $this->memberRepository->upgradePassword($member, $this->passwordHasher->hashPassword($member, $password));

    return null;
  }

  public function initiateResetPassword(Member $member): bool {
    if (!$member->isAccountActivated() || !$this->emailService->canSendEmail()) {
      return false;
    }

    // We verify that we don't have more than 4 in progress reset for this user
    $resetInProgress = $this->memberSecurityCodeRepository->findAllByTrigger($member, MemberSecurityCodeTrigger::resetPassword);
    if (count($resetInProgress) > 3) {
      return false;
    }

    $securityCode = new MemberSecurityCode();
    $securityCode->setTrigger(MemberSecurityCodeTrigger::resetPassword)->setMember($member);

    $this->em->persist($securityCode);
    $this->em->flush();

    // We sent the security code
    $email = $this->emailService->getEmail('security-code.html.twig', 'Changement de mot de passe', ['security_code' => $securityCode->getCode()]);
    $this->emailService->sendEmail($email, $member->getEmail());

    return true;
  }

  public function validateSecurityCode(Member $member, MemberSecurityCodeTrigger $trigger, string $securityCode): bool {
    $securityCodeQuery = $this->memberSecurityCodeRepository->findLastOneForMember($member, $trigger);
    if (!$securityCodeQuery || $securityCodeQuery->getCode() !== trim($securityCode)) {
      return false;
    }

    // We consume all
    $codes = $this->memberSecurityCodeRepository->findAllByTrigger($member, MemberSecurityCodeTrigger::resetPassword);
    foreach ($codes as $code) {
      $this->em->remove($code);
    }
    $this->em->flush();

    return true;
  }
}
