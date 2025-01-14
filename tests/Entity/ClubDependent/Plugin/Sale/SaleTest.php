<?php

namespace App\Tests\Entity\ClubDependent\Plugin\Sale;

use App\Entity\ClubDependent\Plugin\Sale\InventoryItem;
use App\Entity\ClubDependent\Plugin\Sale\Sale;
use App\Tests\Entity\Abstract\AbstractEntityClubLinkedTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\InventoryItemFactory;
use App\Tests\Factory\SaleFactory;
use App\Tests\Factory\SalePaymentModeFactory;
use App\Tests\Factory\SalePurchasedItemFactory;
use App\Tests\Story\_InitStory;
use function Zenstruck\Foundry\faker;

class SaleTest extends AbstractEntityClubLinkedTestCase {
  protected int $TOTAL_SUPER_ADMIN = 10;
  protected int $TOTAL_ADMIN_CLUB_1 = 10;
  protected int $TOTAL_ADMIN_CLUB_2 = 0;
  protected int $TOTAL_SUPERVISOR_CLUB_1 = 10;

  protected function getClassname(): string {
    return Sale::class;
  }

  protected function getRootUrl(): string {
    return "/sales";
  }

  public function initDefaultFixtures(): void {
    SaleFactory::createMany(10);
  }

  public function testCreate(): void {
    $club1 = _InitStory::club_1();
    $inventoryItem = InventoryItemFactory::randomOrCreate(['canBeSold' => true]);
    $paymentMode = SalePaymentModeFactory::createOne(['available' => true]);

    $clubIri = $this->getIriFromResource($club1);
    $inventoryItemIri = $this->getIriFromResource($inventoryItem);
    $paymentModeIri = $this->getIriFromResource($paymentMode);

    $payload = [
      "salePurchasedItems" => [
        [
          "quantity" => 2,
          "item" => $inventoryItemIri
        ],
      ],
      "paymentMode" => $paymentModeIri,
    ];

    $payloadCheck = [
      "club" => [
        '@id' => $clubIri,
      ],
      "salePurchasedItems" => [
        [
          'quantity' => 2,
          'item' => [
            '@id' => $inventoryItemIri,
          ]
        ]
      ],
      "paymentMode" => [
        '@id' => $paymentModeIri,
      ]
    ];

    $this->makeAllLoggedRequests(
      $payloadCheck,
      supervisorClub1Code: ResponseCodeEnum::created,
      adminClub1Code: ResponseCodeEnum::created,
      adminClub2Code: ResponseCodeEnum::forbidden,
      superAdminCode: ResponseCodeEnum::created,
      badgerClub1Code: ResponseCodeEnum::forbidden,
      badgerClub2Code: ResponseCodeEnum::forbidden,
      requestFunction: function (string $level, ?int $id) use ($club1, $payload) {
        $this->makePostRequest($this->getRootWClubUrl($club1), $payload);
      },
    );
  }

  public function testPatch(): void {
    // Update a sale created today
    $this->makeAllLoggedRequests(
      requestFunction: function (string $level, ?int $id) {
        $item = SaleFactory::createOne(['createdAt' => new \DateTimeImmutable()]);
        $this->makePatchRequest($this->getIriFromResource($item), ['comment' => 'test']);
      },
    );

    // Update a sale creating days ago (only admin can)
    $this->makeAllLoggedRequests(
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      requestFunction: function (string $level, ?int $id) {
        $item = SaleFactory::createOne([
          'createdAt' => \DateTimeImmutable::createFromMutable(faker()->dateTimeBetween('-10 days', '-2 days'))
        ]);
        $this->makePatchRequest($this->getIriFromResource($item), ['comment' => 'test']);
      },
    );
  }

  public function testDelete(): void {
    // Deleting a sale created today
    $this->makeAllLoggedRequests(
      supervisorClub1Code: ResponseCodeEnum::no_content,
      adminClub1Code: ResponseCodeEnum::no_content,
      adminClub2Code: ResponseCodeEnum::not_found,
      superAdminCode: ResponseCodeEnum::no_content,
      badgerClub1Code: ResponseCodeEnum::forbidden,
      badgerClub2Code: ResponseCodeEnum::not_found,
      requestFunction: function (string $level, ?int $id) {
        $item = SaleFactory::createOne(['createdAt' => new \DateTimeImmutable()]);
        $this->makeDeleteRequest($this->getIriFromResource($item));
      },
    );

    // Deleting a sale creating days ago (only admin can)
    $this->makeAllLoggedRequests(
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      adminClub1Code: ResponseCodeEnum::no_content,
      adminClub2Code: ResponseCodeEnum::not_found,
      superAdminCode: ResponseCodeEnum::no_content,
      badgerClub1Code: ResponseCodeEnum::forbidden,
      badgerClub2Code: ResponseCodeEnum::not_found,
      requestFunction: function (string $level, ?int $id) {
        $item = SaleFactory::createOne([
          'createdAt' => \DateTimeImmutable::createFromMutable(faker()->dateTimeBetween('-10 days', '-2 days'))
        ]);
        $this->makeDeleteRequest($this->getIriFromResource($item));
      },
    );
  }

  // TODO: Add check item can be sold and paymentMode is available
}
