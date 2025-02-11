<?php

namespace App\Controller\ClubDependent\Plugin\Sale;

use App\Controller\Abstract\SortableController;
use App\Entity\ClubDependent\Plugin\Sale\InventoryCategory;
use App\Repository\ClubDependent\Plugin\Sale\InventoryCategoryRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class InventoryCategoryMove extends SortableController {

  public function __invoke(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] InventoryCategory $inventoryCategory, InventoryCategoryRepository $inventoryCategoryRepository): JsonResponse {
    return $this->move($request, $inventoryCategory, $inventoryCategoryRepository);
  }

}
