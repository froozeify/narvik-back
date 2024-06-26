<?php

namespace App\Service;

use App\Entity\ExternalPresence;
use App\Entity\MemberPresence;
use App\Repository\ExternalPresenceRepository;
use App\Repository\MemberPresenceRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;

class MemberPresenceService {
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly ExternalPresenceRepository $externalPresenceRepository,
    private readonly MemberRepository $memberRepository,
    private readonly MemberPresenceRepository $memberPresenceRepository,
  ) {
  }
  public function importFromExternalPresence(): int {
    $totalImported = 0;

    $presencesWithLicences = $this->externalPresenceRepository->findAllWithLicence();
    /** @var ExternalPresence $extPresence */
    foreach ($presencesWithLicences as $extPresence) {
      $member = $this->memberRepository->findOneByLicence($extPresence->getLicence());
      if (!$member) continue;

      // We check we don't have any record of it already
      $alreadyPresent = $this->memberPresenceRepository->findBy(["member" => $member, "date" => $extPresence->getDate()]);
      if ($alreadyPresent) {
        $this->em->remove($extPresence);
        continue;
      };

      // We create the presence
      $presence = new MemberPresence();
      $presence->setMember($member)
        ->setDate($extPresence->getDate())
        ->setCreatedAt($extPresence->getCreatedAt());

      foreach ($extPresence->getActivities() as $activity) {
        $presence->addActivity($activity);
      }

      $this->em->persist($presence);
      $totalImported++;

      // We delete the old external presence
      $this->em->remove($extPresence);
    }

    $this->em->flush();

    return $totalImported;
  }
}
