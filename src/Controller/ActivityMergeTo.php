<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActivityMergeTo extends AbstractController {

  public function __invoke(Activity $activity, string $targetUuid, ActivityRepository $activityRepository): Response {
    $targetActivity = $activityRepository->findOneByUuid($targetUuid);
    if (!$targetActivity) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Target activity not found");
    }

    if ($activity->getId() === $targetActivity->getId()) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Can't migrate to self activity");
    }

    $activityRepository->mergeAndDelete($activity, $targetActivity);

    return new Response();
  }

}
