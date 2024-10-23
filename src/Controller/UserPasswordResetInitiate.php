<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserPasswordResetInitiate extends AbstractController {

  public function __invoke(Request $request, UserRepository $userRepository, UserService $userService): JsonResponse {
    $payload = $this->checkAndGetJsonValues($request, ['email']);
    $email = $payload['email'];

    $user = $userRepository->findOneByEmail($email);
    if (!$user) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $initialised = $userService->initiateResetPassword($user);
    if (!$initialised) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    return new JsonResponse();
  }

}
