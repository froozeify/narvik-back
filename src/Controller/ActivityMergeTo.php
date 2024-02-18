<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Member;
use App\Repository\ActivityRepository;
use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ActivityMergeTo extends AbstractController {

  public function __invoke(Request $request, Activity $activity, Activity $targetActivity, ActivityRepository $activityRepository): Activity {
    if ($activity->getId() === $targetActivity->getId()) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Can't migrate to self activity");
    }

    $activityRepository->mergeAndDelete($activity, $targetActivity);

    return $targetActivity;
  }

}
