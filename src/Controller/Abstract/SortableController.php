<?php

namespace App\Controller\Abstract;

use App\Entity\Interface\SortableEntityInterface;
use App\Entity\Member;
use App\Repository\Interface\SortableRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SortableController extends AbstractController {
  public function move(Request $request, SortableEntityInterface $entity, SortableRepositoryInterface $repository) {
    $user = $this->getUser();
    if (!$user instanceof Member) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $payload = $this->checkAndGetJsonValues($request, ['direction']);
    $direction = strtolower((string) $payload['direction']);

    if (!in_array($direction, ['up', 'down'])) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Direction must be 'up' or 'down'");
    }

    if ($direction === 'up') {
      $repository->moveUp($entity);
    } else {
      $repository->moveDown($entity);
    }

    return new JsonResponse();
  }
}
