<?php

namespace App\Tests\Entity;

use App\Entity\AgeCategory;
use App\Tests\Entity\Abstract\AbstractEntityTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\AgeCategoryFactory;
use App\Tests\Story\AgeCategoryStory;

class AgeCategoryTest extends AbstractEntityTestCase {
  protected int $TOTAL_SUPER_ADMIN = 3;

  protected function getClassname(): string {
    return AgeCategory::class;
  }

  protected function getRootUrl(): string {
    return '/age-categories';
  }

  public function initDefaultFixtures(): void {
    AgeCategoryStory::load();
  }


  public function testCreate(): void {
    $payload = [
      "name" => 'Dame 1',
      "code" => 'D1',
    ];

    // Only super admin can create
    $this->makeAllLoggedRequests(
      $payload,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::created,
      badgerClub2Code: ResponseCodeEnum::forbidden,
      requestFunction: function (string $level, ?int $id) use ($payload) {
        $this->makePostRequest($this->getRootUrl(), $payload);
      }
    );

  }

  public function testPatch(): void {
    $ageCategory = AgeCategoryStory::getRandom('age_categories');
    $iri = $this->getIriFromResource($ageCategory);

    $payload = [
      "name" => 'Updated',
    ];

    // Only super admin can update
    $this->makeAllLoggedRequests(
      $payload,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      badgerClub2Code: ResponseCodeEnum::forbidden,
      requestFunction: function (string $level, ?int $id) use ($iri, $payload) {
        $this->makePatchRequest($iri, $payload);
      }
    );
  }

  public function testDelete(): void {
    // Only super admin can delete
    $this->makeAllLoggedRequests(
      null,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      superAdminCode: ResponseCodeEnum::no_content,
      badgerClub2Code: ResponseCodeEnum::forbidden,
      requestFunction: function (string $level, ?int $id) {
        $ageCategory = AgeCategoryFactory::createOne([
          "name" => 'Dame 1',
          "code" => 'D1',
        ]);
        $iri = $this->getIriFromResource($ageCategory);
        $this->makeDeleteRequest($iri);
      }
    );
  }
}
