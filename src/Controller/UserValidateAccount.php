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

class UserValidateAccount extends AbstractController {

  public function __invoke(Request $request, UserRepository $userRepository, UserService $userService): JsonResponse {
    $payload = $this->checkAndGetJsonValues($request, ['email', 'securityCode']);
    $email = $payload['email'];
    $securityCode = $payload['securityCode'];

    $user = $userRepository->findOneByEmail($email);
    if (!$user) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $validated = $userService->validateSecurityCode($user, UserSecurityCodeTrigger::accountValidation, $securityCode);
    if (!$validated) {
      $userService->initiateAccountValidation($user); // We trigger a new password query
      throw new HttpException(Response::HTTP_BAD_REQUEST, "A new security code has been sent.");
    }

    $userService->activateAccount($user);
    return new JsonResponse();
  }

}
