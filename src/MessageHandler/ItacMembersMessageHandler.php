<?php

namespace App\MessageHandler;

use App\Entity\AgeCategory;
use App\Entity\Member;
use App\Entity\MemberSeason;
use App\Entity\Season;
use App\Enum\ItacCsvHeaderMapping;
use App\Enum\ClubRole;
use App\Message\ItacMembersMessage;
use App\Repository\AgeCategoryRepository;
use App\Repository\MemberRepository;
use App\Repository\MemberSeasonRepository;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\ResetInterface;

#[AsMessageHandler]
class ItacMembersMessageHandler implements ResetInterface {
  private array $members = [];
  private array $membersEmail = [];
  private array $seasons = [];
  private array $ageCategories = [];

  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly MemberRepository $memberRepository,
    private readonly SeasonRepository $seasonRepository,
    private readonly AgeCategoryRepository $ageCategoryRepository,
    private readonly MemberSeasonRepository $memberSeasonRepository,
    private readonly ValidatorInterface $validator,) {
  }

  public function reset(): void {
    $this->members = [];
    $this->membersEmail = [];
    $this->seasons = [];
    $this->ageCategories = [];
  }


  public function __invoke(ItacMembersMessage $message): void {
//    $response = [
//      'failed' => 0,
//      'success' => 0
//    ];


    foreach ($message->getRecords() as $record) {
      // We get the user, if he exists

      /** @var Member|null $member */
      $member = null;
      if (array_key_exists($record[ItacCsvHeaderMapping::LICENCE->value], $this->members)) {
        $member = $this->members[$record[ItacCsvHeaderMapping::LICENCE->value]];
      } else {
        /** @var Member|null $member */
        $member = $this->memberRepository->findOneByLicence($record[ItacCsvHeaderMapping::LICENCE->value]);

        if (!$member) {
          $member = new Member();
          $member->setLicence($record[ItacCsvHeaderMapping::LICENCE->value]);
          $member->setRole(ClubRole::member);
        }
        $this->members[$record[ItacCsvHeaderMapping::LICENCE->value]] = $member;
      }


      if (!empty($record[ItacCsvHeaderMapping::PHONE->value])) {
        $member->setPhone(str_pad(str_replace(" ", "", $record[ItacCsvHeaderMapping::PHONE->value]), 10, "0", STR_PAD_LEFT));
      }

      if (!empty($record[ItacCsvHeaderMapping::MOBILE_PHONE->value])) {
        $member->setMobilePhone(str_pad(str_replace(" ", "", $record[ItacCsvHeaderMapping::MOBILE_PHONE->value]), 10, "0", STR_PAD_LEFT));
      }

      $email = $record[ItacCsvHeaderMapping::EMAIL->value];
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
        ->setGender($record[ItacCsvHeaderMapping::GENDER->value])
        ->setLastname($record[ItacCsvHeaderMapping::LASTNAME->value])
        ->setFirstname($record[ItacCsvHeaderMapping::FIRSTNAME->value])
        ->setBirthday(\DateTime::createFromFormat("d/m/Y", $record[ItacCsvHeaderMapping::BIRTHDAY->value]))
        ->setHandisport($this->toBoolean($record[ItacCsvHeaderMapping::HANDISPORT->value]))
        ->setDeceased($this->toBoolean($record[ItacCsvHeaderMapping::DECEASED->value]))

        ->setPostal1($record[ItacCsvHeaderMapping::POSTAL_1->value])
        ->setPostal2($record[ItacCsvHeaderMapping::POSTAL_2->value])
        ->setPostal3($record[ItacCsvHeaderMapping::POSTAL_3->value])
        ->setCity($record[ItacCsvHeaderMapping::CITY->value])
        ->setCountry($record[ItacCsvHeaderMapping::COUNTRY->value])

        ->setBlacklisted($record[ItacCsvHeaderMapping::BLACKLISTED->value] !== 'Autorisé')
        ->setLicenceState($record[ItacCsvHeaderMapping::LICENCE_STATE->value])
        ->setLicenceType($record[ItacCsvHeaderMapping::LICENCE_TYPE->value]);

      if (is_numeric($record[ItacCsvHeaderMapping::ZIP_CODE->value])) {
        $member
          ->setZipCode(intval($record[ItacCsvHeaderMapping::ZIP_CODE->value]));
      }

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
//    dump($response);
  }

  private function toBoolean($value): bool {
    return is_bool($value) ? $value : !in_array(strtolower((string) $value), ['', '0', 'false']);
  }

  private function defineMemberSeason(Member $member, array $record): void {
    $memberSeason = new MemberSeason();
    $memberSeason->setMember($member);

    $seasonCsv = $record[ItacCsvHeaderMapping::SEASON->value];
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

    $ageCodeCsv = $record[ItacCsvHeaderMapping::AGE_CODE->value];
    // We check if already in our array
    /** @var AgeCategory|null $ageCategory */
    $ageCategory = null;
    if (array_key_exists($ageCodeCsv, $this->ageCategories)) {
      $ageCategory = $this->ageCategories[$ageCodeCsv];
    } else {
      // We get it in the db
      $ageCategoryDb = $this->ageCategoryRepository->findOneBy(["code" => $ageCodeCsv]);
      if ($ageCategoryDb) {
        $this->ageCategories[$ageCodeCsv] = $ageCategoryDb;
        $ageCategory = $this->ageCategories[$ageCodeCsv];
      } else {
        // We create it
        $ageCategory = new AgeCategory();
        $ageCategory
          ->setName($record[ItacCsvHeaderMapping::AGE_CATEGORY->value])
          ->setCode($ageCodeCsv);
        $this->entityManager->persist($ageCategory);
        $this->ageCategories[$ageCodeCsv] = $ageCategory;
      }
    }

    if ($ageCategory->getId()) {
      $ref = $this->entityManager->getReference(AgeCategory::class, $ageCategory->getId());
      if ($ref) {
        $ageCategory = $ref;
      }
    }
    $memberSeason->setAgeCategory($ageCategory);

    // If this memberSeason already exist (ignore ageCategory) we don't create it
    $msDb = $this->memberSeasonRepository->findOneBy(["member" => $memberSeason->getMember(), "season" => $memberSeason->getSeason()]);
    if ($msDb) {
      return;
    }

    $this->entityManager->persist($memberSeason);
  }
}
