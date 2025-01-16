<?php

namespace App\Tests\Entity\ClubDependent\Plugin\Sale;

use App\Entity\ClubDependent\Plugin\Sale\InventoryItem;
use App\Tests\Entity\Abstract\AbstractEntityClubLinkedTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\InventoryItemFactory;
use App\Tests\Story\_InitStory;

class InventoryItemTest extends AbstractEntityClubLinkedTestCase {
  protected int $TOTAL_SUPER_ADMIN = 10;
  protected int $TOTAL_ADMIN_CLUB_1 = 10;
  protected int $TOTAL_ADMIN_CLUB_2 = 0;
  protected int $TOTAL_SUPERVISOR_CLUB_1 = 10;

  protected function getClassname(): string {
    return InventoryItem::class;
  }

  protected function getRootUrl(): string {
    return "/inventory-items";
  }

  public function initDefaultFixtures(): void {
    InventoryItemFactory::createMany(10);
  }

  public function testCreate(): void {
    $payloadCheck = [];
    $this->makeAllLoggedRequests(
      $payloadCheck,
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      adminClub1Code: ResponseCodeEnum::created,
      adminClub2Code: ResponseCodeEnum::forbidden,
      superAdminCode: ResponseCodeEnum::created,
      badgerClub1Code: ResponseCodeEnum::forbidden,
      badgerClub2Code: ResponseCodeEnum::forbidden,
      requestFunction: function (string $level, ?int $id) use (&$payloadCheck) {
        $club1 = _InitStory::club_1();
        $payload = [
          "name" => "Test$id",
          "sellingPrice" => "10.00",
        ];

        $payloadCheck = $payload;
        $this->makePostRequest($this->getRootWClubUrl($club1), $payload);
      },
    );
  }

  public function testPatch(): void {
    $payloadCheck = [];
    $this->makeAllLoggedRequests(
      $payloadCheck,
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      requestFunction: function (string $level, ?int $id) use (&$payloadCheck) {
        $item = InventoryItemFactory::createOne();

        $payloadCheck = [
          "name" => "My new name$id"
        ];

        $this->makePatchRequest($this->getIriFromResource($item), $payloadCheck);
      },
    );
  }

  public function testDelete(): void {
    $this->makeAllLoggedRequests(
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      adminClub1Code: ResponseCodeEnum::no_content,
      adminClub2Code: ResponseCodeEnum::not_found,
      superAdminCode: ResponseCodeEnum::no_content,
      badgerClub1Code: ResponseCodeEnum::forbidden,
      badgerClub2Code: ResponseCodeEnum::not_found,
      requestFunction: function (string $level, ?int $id) {
        $item = InventoryItemFactory::createOne();
        $this->makeDeleteRequest($this->getIriFromResource($item));
      },
    );
  }
}
