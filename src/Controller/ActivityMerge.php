<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Entity\ClubDependent\Activity;
use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActivityMerge extends AbstractController {

  public function __invoke(Request $request, Activity $activity, ActivityRepository $activityRepository): Response {
    $json = $this->checkAndGetJsonValues($request, ['target']);

    $targetActivity = $activityRepository->findOneByUuid($json['target']);
    if (!$targetActivity) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Target activity not found");
    }

    if ($activity->getId() === $targetActivity->getId()) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Can't migrate to self activity");
    }

    if ($activity->getClub() !== $targetActivity->getClub()) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Activity club does not match target activity club");
    }

    $activityRepository->mergeAndDelete($activity, $targetActivity);

    return new Response();
  }

}
