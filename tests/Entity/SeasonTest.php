<?php

namespace App\Tests\Entity;

use App\Entity\AgeCategory;
use App\Entity\Season;
use App\Tests\Entity\Abstract\AbstractEntityTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\AgeCategoryFactory;
use App\Tests\Factory\SeasonFactory;
use App\Tests\Story\AgeCategoryStory;
use App\Tests\Story\SeasonStory;

class SeasonTest extends AbstractEntityTestCase {
  protected int $TOTAL_SUPER_ADMIN = 6;

  protected function getClassname(): string {
    return Season::class;
  }

  protected function getRootUrl(): string {
    return '/seasons';
  }

  public function initDefaultFixtures(): void {
    SeasonStory::load();
  }


  public function testCreate(): void {
    $payload = [
      "name" => '2018/2019',
    ];

    // Only super admin can create
    $this->makeAllLoggedRequests(
      $payload,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::created,
      requestFunction: function (string $level, ?int $id) use ($payload) {
        $this->makePostRequest($this->getRootUrl(), $payload);
      }
    );

  }

  public function testPatch(): void {
    $season = SeasonStory::getRandom('seasons');
    $iri = $this->getIriFromResource($season);

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
      requestFunction: function (string $level, ?int $id) {
        $season = SeasonFactory::createOne([
          "name" => '2018/2019',
        ]);
        $iri = $this->getIriFromResource($season);
        $this->makeDeleteRequest($iri);
      }
    );
  }
}
