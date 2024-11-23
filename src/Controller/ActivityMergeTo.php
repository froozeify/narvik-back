<?php

namespace App\Controller;

use App\Entity\ClubDependent\Activity;
use App\Enum\ClubRole;
use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActivityMergeTo extends AbstractController {

  public function __invoke(Activity $activity, string $targetUuid, ActivityRepository $activityRepository): Response {
    // We verify the user has the permission
    // $activity is always loaded here (UserClubExtension is only called on GET query)
    $this->denyAccessUnlessGranted(ClubRole::admin->value, $activity);

    $targetActivity = $activityRepository->findOneByUuid($targetUuid);
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
