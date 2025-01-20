<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Enum\UserSecurityCodeTrigger;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserPasswordReset extends AbstractController {

  public function __invoke(Request $request, UserRepository $userRepository, UserService $userService): JsonResponse {
    $payload = $this->checkAndGetJsonValues($request, ['email', 'password', 'securityCode']);
    $email = $payload['email'];
    $password = $payload['password'];
    $securityCode = $payload['securityCode'];

    $user = $userRepository->findOneByEmail($email);
    if (!$user) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $validated = $userService->validateSecurityCode($user, UserSecurityCodeTrigger::resetPassword, $securityCode);
    if (!$validated) {
      $userService->initiateResetPassword($user); // We trigger a new password query
      throw new HttpException(Response::HTTP_BAD_REQUEST, "A new security code has been sent.");
    }

    $changeError = $userService->changeUserPassword($user, $password);
    if ($changeError) {
      $userService->initiateResetPassword($user); // We trigger a new password query
      throw new HttpException(Response::HTTP_BAD_REQUEST, $changeError . " A new security code has been sent.");
    }

    return new JsonResponse();
  }

}
