<?php

namespace App\Controller;

use App\Controller\Abstract\SortableController;
use App\Entity\InventoryCategory;
use App\Repository\InventoryCategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class InventoryCategoryMove extends SortableController {

  public function __invoke(Request $request, InventoryCategory $inventoryCategory, InventoryCategoryRepository $inventoryCategoryRepository): JsonResponse {
    return $this->move($request, $inventoryCategory, $inventoryCategoryRepository);
  }

}
