<?php

namespace App\MessageHandler;

use App\Entity\AgeCategory;
use App\Entity\Member;
use App\Entity\MemberSeason;
use App\Entity\Season;
use App\Enum\ItacSecondaryClubCsvHeaderMapping;
use App\Enum\MemberRole;
use App\Message\ItacSecondaryClubMembersMessage;
use App\Repository\AgeCategoryRepository;
use App\Repository\MemberRepository;
use App\Repository\MemberSeasonRepository;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\ResetInterface;

#[AsMessageHandler]
class ItacSecondaryClubMembersMessageHandler implements ResetInterface {
  private array $members = [];
  private array $membersEmail = [];
  private array $seasons = [];
  private array $ageCategories = [];

  public function __construct(
    private EntityManagerInterface $entityManager,
    private MemberRepository $memberRepository,
    private SeasonRepository $seasonRepository,
    private AgeCategoryRepository $ageCategoryRepository,
    private MemberSeasonRepository $memberSeasonRepository,
    private ValidatorInterface $validator,) {
  }

  public function reset(): void {
    $this->members = [];
    $this->membersEmail = [];
    $this->seasons = [];
    $this->ageCategories = [];
  }


  public function __invoke(ItacSecondaryClubMembersMessage $message) {

    foreach ($message->getRecords() as $record) {
      // We get the user, if he exists

//      dump(ItacSecondaryClubCsvHeaderMapping::LICENCE->value);
//      dump($record[ItacSecondaryClubCsvHeaderMapping::LICENCE->value]);

      /** @var Member|null $member */
      $member = null;
      if (array_key_exists($record[ItacSecondaryClubCsvHeaderMapping::LICENCE->value], $this->members)) {
        $member = $this->members[$record[ItacSecondaryClubCsvHeaderMapping::LICENCE->value]];
      } else {
        /** @var Member|null $member */
        $member = $this->memberRepository->findOneByLicence($record[ItacSecondaryClubCsvHeaderMapping::LICENCE->value]);

        if (!$member) {
          $member = new Member();
          $member->setLicence($record[ItacSecondaryClubCsvHeaderMapping::LICENCE->value]);
          $member->setRole(MemberRole::member);
        }
        $this->members[$record[ItacSecondaryClubCsvHeaderMapping::LICENCE->value]] = $member;
      }


      if (!empty($record[ItacSecondaryClubCsvHeaderMapping::PHONE->value])) {
        $member->setPhone(str_pad(str_replace(" ", "", $record[ItacSecondaryClubCsvHeaderMapping::PHONE->value]), 10, "0", STR_PAD_LEFT));
      }

      if (!empty($record[ItacSecondaryClubCsvHeaderMapping::MOBILE_PHONE->value])) {
        $member->setMobilePhone(str_pad(str_replace(" ", "", $record[ItacSecondaryClubCsvHeaderMapping::MOBILE_PHONE->value]), 10, "0", STR_PAD_LEFT));
      }

      $email = $record[ItacSecondaryClubCsvHeaderMapping::EMAIL->value];
      if ($email) {
        // We check the email is not already define to another member
        $dbMemberEmail = $this->memberRepository->findOneBy(["email" => $email]);
        if ($dbMemberEmail) {
          $this->membersEmail[$email] = $dbMemberEmail;
        }

        if (!array_key_exists($email, $this->membersEmail)) {
          $member->setEmail($email);
          $this->membersEmail[$email] = $member;
        }
      }

      $member
        ->setGender($record[ItacSecondaryClubCsvHeaderMapping::GENDER->value])
        ->setLastname($record[ItacSecondaryClubCsvHeaderMapping::LASTNAME->value])
        ->setFirstname($record[ItacSecondaryClubCsvHeaderMapping::FIRSTNAME->value])

        ->setPostal1($record[ItacSecondaryClubCsvHeaderMapping::POSTAL_1->value])
        ->setPostal2($record[ItacSecondaryClubCsvHeaderMapping::POSTAL_2->value])
        ->setPostal3($record[ItacSecondaryClubCsvHeaderMapping::POSTAL_3->value])
        ->setZipCode($record[ItacSecondaryClubCsvHeaderMapping::ZIP_CODE->value])
        ->setCity($record[ItacSecondaryClubCsvHeaderMapping::CITY->value]);

      $errors = $this->validator->validate($member);
      if (count($errors) > 0) {
        // TODO: Will be use when Message will contain a batch id so we can have more detailed logs in db
//        $response['failed'] += 1;
//        $response['failed_messages'][] = [
//          'licence' => $member->getLicence(),
//          'email' => $member->getEmail(),
//          'lastname' => $member->getLastname(),
//          'firstname' => $member->getFirstname(),
//          'message' => (string) $errors
//        ];
        continue;
      }

      // TODO: Will be use when Message will contain a batch id so we can have more detailed logs in db
      // $response['success'] += 1;
      // We persist it
      $this->entityManager->persist($member);
      // We set his seasons
      $this->defineMemberSeason($member, $record);
    }

    $this->entityManager->flush();
  }

  private function toBoolean($value): bool {
    return is_bool($value) ? $value : !in_array(strtolower($value), ['', '0', 'false']);
  }

  private function defineMemberSeason(Member $member, array $record): void {
    $memberSeason = new MemberSeason();
    $memberSeason->setMember($member);

    $seasonCsv = $record[ItacSecondaryClubCsvHeaderMapping::SEASON->value];
    // We check if already in our array
    /** @var Season|null $season */
    $season = null;
    if (array_key_exists($seasonCsv, $this->seasons)) {
      $season = $this->seasons[$seasonCsv];
    } else {
      // We get it in the db
      $seasonDb = $this->seasonRepository->findOneBy(["name" => $seasonCsv]);
      if ($seasonDb) {
        $this->seasons[$seasonCsv] = $seasonDb;
        $season = $this->seasons[$seasonCsv];
      } else {
        // We create it
        $season = new Season();
        $season->setName($seasonCsv);
        $this->entityManager->persist($season);
        $this->seasons[$seasonCsv] = $season;
      }
    }

    if ($season->getId()) {
      $ref = $this->entityManager->getReference(Season::class, $season->getId());
      if ($ref) {
        $season = $ref;
      }
    }
    $memberSeason->setSeason($season);

    $ageCodeNameCsv = $record[ItacSecondaryClubCsvHeaderMapping::AGE_CATEGORY->value];
    // We check if already in our array
    /** @var AgeCategory|null $ageCategory */
    $ageCategory = null;
    if (array_key_exists($ageCodeNameCsv, $this->ageCategories)) {
      $ageCategory = $this->ageCategories[$ageCodeNameCsv];
    } else {
      // We get it in the db
      $ageCategoryDb = $this->ageCategoryRepository->findOneBy(["name" => $ageCodeNameCsv]);
      if ($ageCategoryDb) {
        $this->ageCategories[$ageCodeNameCsv] = $ageCategoryDb;
        $ageCategory = $this->ageCategories[$ageCodeNameCsv];
      }
    }

    if (!$ageCategory) {
      return;
    }


    if ($ageCategory->getId()) {
      $ref = $this->entityManager->getReference(AgeCategory::class, $ageCategory->getId());
      if ($ref) {
        $ageCategory = $ref;
      }
    }
    $memberSeason->setIsSecondaryClub(true);
    $memberSeason->setAgeCategory($ageCategory);

    // If this memberSeason already exist (ignore ageCategory) we don't create it
    $msDb = $this->memberSeasonRepository->findOneBy(["member" => $memberSeason->getMember(), "season" => $memberSeason->getSeason()]);
    if ($msDb) {
      return;
    }

    $this->entityManager->persist($memberSeason);
  }
}
