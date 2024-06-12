<?php

namespace App\Controller;

use App\Entity\InventoryCategory;
use App\Entity\Member;
use App\Repository\InventoryCategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InventoryCategoryMove extends AbstractController {

  public function __invoke(Request $request, InventoryCategory $inventoryCategory, InventoryCategoryRepository $inventoryCategoryRepository): JsonResponse {
    $user = $this->getUser();
    if (!$user instanceof Member) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $payload = $this->checkAndGetJsonValues($request, ['direction']);
    $direction = strtolower($payload['direction']);

    if (!in_array($direction, ['up', 'down'])) {
        throw new HttpException(Response::HTTP_BAD_REQUEST, "Direction must be 'up' or 'down'");
    }

    dump($inventoryCategory);

    if ($direction === 'up') {
      $inventoryCategoryRepository->moveUp($inventoryCategory);
    } else {
      $inventoryCategoryRepository->moveDown($inventoryCategory);
    }

    dump($inventoryCategory);

    return new JsonResponse();
  }

}
