<?php

namespace App\Importer;

use App\Entity\MemberPresence;
use App\Importer\Model\AbstractImportedItemResult;
use App\Importer\Model\ErrorImportedItem;
use App\Importer\Model\SuccessImportedItem;
use App\Importer\Model\WarningImportedItem;
use App\Repository\ActivityRepository;
use App\Repository\MemberPresenceRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportMemberPresence extends AbstractCsvImporter {
  const COL_LICENCE = 'member.licence';
  const COL_DATE = 'date';
  const COL_ACTIVITIES = 'activities';

  const ERROR_CODES = [
    // 1xx: Error
    100 => ["errorCode" => "member-not-found", "reason" => "Member not found"],
    101 => ["errorCode" => "date-wrong-format", "reason" => "Date wrongly formatted"],

    // 2xx: Warning
    200 => ["errorCode" => "presence-already-registered", "reason" => "Member presence already registered. Safely ignoring it."],

  ];

  public function __construct(
    EntityManagerInterface $em,
    ValidatorInterface $validator,
    private MemberRepository $memberRepository,
    private ActivityRepository $activityRepository,
    private MemberPresenceRepository $memberPresenceRepository,
  ) {
    parent::__construct($em, $validator);
  }


  protected function getRequiredCols(): array {
    return [
      self::COL_LICENCE,
      self::COL_DATE
    ];
  }

  protected function callbackAfterRowsParsed(): void {
    $this->em->flush(); // Memory optimisation
  }

  protected function callbackEveryNParsedRows(): void {
    $this->em->flush();
  }

  protected function addItem(array &$row): AbstractImportedItemResult {
    $licence = $this->getCurrentRowValue(self::COL_LICENCE);
    if (empty($licence)) return new ErrorImportedItem($licence, self::ERROR_CODES[100]);

    $member = $this->memberRepository->findOneByLicence($licence);
    if (!$member) return new ErrorImportedItem($licence, self::ERROR_CODES[100]);

    $date = $this->getCurrentRowValue(self::COL_DATE);
    if (!$date) return new ErrorImportedItem($licence, self::ERROR_CODES[101]);

    $date = new \DateTimeImmutable($date);

    // We check the presence is not already registered
    $existingPresence = $this->memberPresenceRepository->findOneByDay($member, \DateTime::createFromImmutable($date));
    if ($existingPresence) {
      $warning = new WarningImportedItem($member->getLicence());
      $warning->addWarning(self::ERROR_CODES[200]);
      return $warning; // We return it since we stop the import for it
    }

    $memberPresence = new MemberPresence();
    $memberPresence
      ->setMember($member)
      ->setDate($date);

    $this->callbackForRowMultiCol(self::COL_ACTIVITIES, function($v) use ($memberPresence) {
      foreach ($v as $value) {
        if (!array_key_exists("name", $value) || empty($value["name"])) continue;
        $activityName = $value["name"];
        $activity = $this->activityRepository->findOneByName($activityName);

        if (!$activity) continue; // We don't create activity, we just ignore

        $memberPresence->addActivity($activity);
      }
    });

    $this->em->persist($memberPresence);

    return new SuccessImportedItem([
      "id" => $memberPresence->getId()
    ]);
  }
}
