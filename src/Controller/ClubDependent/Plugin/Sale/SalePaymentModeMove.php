<?php

namespace App\Controller\ClubDependent\Plugin\Sale;

use App\Controller\Abstract\SortableController;
use App\Entity\ClubDependent\Plugin\Sale\SalePaymentMode;
use App\Repository\ClubDependent\Plugin\Sale\SalePaymentModeRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SalePaymentModeMove extends SortableController {

  public function __invoke(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] SalePaymentMode $salePaymentMode, SalePaymentModeRepository $salePaymentModeRepository): JsonResponse {
    return $this->move($request, $salePaymentMode, $salePaymentModeRepository);
  }

}
