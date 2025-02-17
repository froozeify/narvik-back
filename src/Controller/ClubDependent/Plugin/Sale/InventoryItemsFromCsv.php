<?php

namespace App\Controller\ClubDependent\Plugin\Sale;

use App\Controller\Abstract\AbstractClubDependentController;
use App\Importer\ImportInventoryItem;
use App\Importer\ImportSale;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InventoryItemsFromCsv extends AbstractClubDependentController {

  public function __invoke(Request $request, ImportInventoryItem $importInventorySale): JsonResponse {
    /** @var UploadedFile|null $uploadedFile */
    $uploadedFile = $request->files->get('file');
    if (!$uploadedFile) {
      throw new BadRequestHttpException('"file" is required');
    }

    if (strtolower($uploadedFile->getClientOriginalExtension()) !== "csv") {
      throw new BadRequestHttpException('The "file" must be a csv');
    }

    $importInventorySale->setClub($this->getQueryClub());
    $response = $importInventorySale->fromFile($uploadedFile);

    return new JsonResponse($response);
  }

}
