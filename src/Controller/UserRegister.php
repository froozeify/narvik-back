<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Entity\User;
use App\Enum\UserRole;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserRegister extends AbstractController {

  public function __invoke(Request $request, UserRepository $userRepository, UserService $userService, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse {
    $payload = $this->checkAndGetJsonValues($request, ['email', 'firstname', 'lastname', 'password']);
    $email = $payload['email'];
    $firstname = $payload['firstname'];
    $lastname = $payload['lastname'];
    $password = $payload['password'];

    $user = $userRepository->findOneByEmail($email);
    if ($user) {
      if ($user->isAccountActivated()) {
        throw new HttpException(Response::HTTP_BAD_REQUEST, 'User already registered.');
      }
    } else {
      $user = new User();
      $user
        ->setRole(UserRole::user)
        ->setEmail($email)
        ->setFirstname($firstname)
        ->setlastname($lastname)
        ->setPlainPassword($password);

      $errors = $validator->validate($user);
      if (count($errors) > 0) {
        throw new HttpException(Response::HTTP_BAD_REQUEST, $errors);
      }

      $em->persist($user);
      $em->flush();
    }

    $initialised = $userService->initiateAccountValidation($user);
    if (!$initialised) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    return new JsonResponse();
  }

}
