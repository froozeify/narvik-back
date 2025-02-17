<?php

namespace App\State;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\ClubDependent\Plugin\Sale\InventoryItemHistoryRepository;
use App\Repository\ClubDependent\Plugin\Sale\InventoryItemRepository;
use App\Repository\ClubRepository;

final class InventoryItemHistoryProvider implements ProviderInterface {
  public function __construct(
    private readonly ClubRepository $clubRepository,
    private readonly InventoryItemRepository $inventoryItemRepository,
    private readonly InventoryItemHistoryRepository $inventoryItemHistoryRepository,
  ) {
  }

  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null {
    if ($operation instanceof GetCollection) {
      $club = $this->clubRepository->findOneByUuid($uriVariables["clubUuid"]);
      if (!$club) {
        return null;
      }

      $item = $this->inventoryItemRepository->findOneByClubAndUuid($club, $uriVariables["itemUuid"]);
      if (!$item) {
        return null;
      }

      return $this->inventoryItemHistoryRepository->findBy([
        "item" => $item,
      ]);
    }
    return null;
  }

}
