<?php

namespace App\MessageHandler;

use App\Entity\Activity;
use App\Entity\ExternalPresence;
use App\Entity\Member;
use App\Entity\MemberPresence;
use App\Message\CerberePresencesDateMessage;
use App\Repository\ActivityRepository;
use App\Repository\ExternalPresenceRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\ResetInterface;

#[AsMessageHandler]
class CerberePresencesDateMessageHandler implements ResetInterface {
  /** @var Activity[] */
  private array $activities = [];
  /** @var Member[]  */
  private array $members = [];

  private array $externalMembers = [];

  public function __construct(
    private EntityManagerInterface $entityManager,
    private MemberRepository $memberRepository,
    private ExternalPresenceRepository $externalPresenceRepository,
    private ActivityRepository $activityRepository,
    private ValidatorInterface $validator,) {
  }

  public function reset(): void {
    $this->members = [];
    $this->activities = [];
    $this->externalMembers = [];
  }

  public function __invoke(CerberePresencesDateMessage $message) {
    $this->generateActivitiesList();
    $this->generateMembersList();
    $this->generateExternalPresenceList();

    $date = $message->getDate();
    foreach ($message->getDatas() as $data) {
      $licence = $data['licence'];
      $activities = $data['activities'];

      if (!array_key_exists($licence, $this->members)) {
        $this->registerExternalPresence($licence, $data['fullName'], $activities, $date);
        continue;
      }

      $member = $this->members[$licence];

      // We verify the presence is not already saved
      $presenceAlreadyRegistered = false;
      foreach ($member->getMemberPresences() as $dbMemberPresence) {
        if ($dbMemberPresence->getDate()->format("Y-m-d") === $date->format("Y-m-d")) {
          $presenceAlreadyRegistered = true;
          break;
        }
      }

      if ($presenceAlreadyRegistered) {
        continue;
      }

      $memberPresence = new MemberPresence();
      $memberPresence
        ->setMember($member)
        ->setDate($date);


      foreach ($activities as $activity) {
        $memberPresence->addActivity($this->activities[$activity]);
      }

      $this->entityManager->persist($memberPresence);
    }

    $this->entityManager->flush();
  }

  private function generateMembersList(): void {
    $this->members = [];
    foreach ($this->memberRepository->findAll() as $dbMember) {
      $this->members[$dbMember->getLicence()] = $dbMember;
    }
  }

  private function generateExternalPresenceList(): void {
    $this->externalMembers = [];
    foreach ($this->externalPresenceRepository->findAll() as $dbExtPresence) {
      if ($dbExtPresence->getLicence() && $dbExtPresence->getDate()) {
        $this->externalMembers[$dbExtPresence->getLicence()][] = $dbExtPresence->getDate()->format("Y-m-d");
      }
    }
  }

  private function generateActivitiesList(): void {
    $this->activities = [];
    foreach ($this->activityRepository->findAll() as $activity) {
      $this->activities[$activity->getName()] = $activity;
    }
  }

  private function registerExternalPresence(string $licence, string $fullName, array $activities, \DateTimeImmutable $date): void {
    // We verify the presence is not already saved
    $presenceAlreadyRegistered = false;
    if (array_key_exists($licence, $this->externalMembers)) {
      foreach ($this->externalMembers[$licence] as $presenceDate) {
        if ($presenceDate === $date->format("Y-m-d")) {
          return; // We stop the registration
        }
      }
    }

    $this->externalMembers[$licence][] = $date->format("Y-m-d");

    $fullName = explode(' ', $fullName, 2);

    $externalPresence = new ExternalPresence();
    $externalPresence
      ->setLicence($licence)
      ->setLastname($fullName[0])
      ->setFirstname($fullName[1])
      ->setDate($date);


    foreach ($activities as $activity) {
      $externalPresence->addActivity($this->activities[$activity]);
    }

    $this->entityManager->persist($externalPresence);
  }
}
