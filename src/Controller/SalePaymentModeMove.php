<?php

namespace App\Controller;

use App\Controller\Abstract\SortableController;
use App\Entity\SalePaymentMode;
use App\Repository\SalePaymentModeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SalePaymentModeMove extends SortableController {

  public function __invoke(Request $request, SalePaymentMode $salePaymentMode, SalePaymentModeRepository $salePaymentModeRepository): JsonResponse {
    return $this->move($request, $salePaymentMode, $salePaymentModeRepository);
  }

}
