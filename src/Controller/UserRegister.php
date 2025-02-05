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

class UserRegister extends AbstractController {

  public function __invoke(Request $request, UserRepository $userRepository, UserService $userService): JsonResponse {
    $payload = $this->checkAndGetJsonValues($request, ['email', 'securityCode', 'firstname', 'lastname', 'password']);
    $email = $payload['email'];
    $securityCode = $payload['securityCode'];
    $firstname = $payload['firstname'];
    $lastname = $payload['lastname'];
    $password = $payload['password'];

    $user = $userRepository->findOneByEmail($email);
    if (!$user) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $validated = $userService->validateSecurityCode($user, UserSecurityCodeTrigger::accountValidation, $securityCode);
    if (!$validated) {
      $userService->initiateAccountValidation($user); // We trigger a new password query
      throw new HttpException(Response::HTTP_BAD_REQUEST, "A new security code has been sent.");
    }

    $userService->activateAccount($user, $firstname, $lastname, $password);
    return new JsonResponse();
  }

}
