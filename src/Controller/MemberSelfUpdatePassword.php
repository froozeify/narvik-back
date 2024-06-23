<?php

namespace App\Controller;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberSelfUpdatePassword extends AbstractController {

  public function __invoke(Request $request, UserPasswordHasherInterface $passwordHasher, MemberRepository $memberRepository): JsonResponse {
    $user = $this->getUser();
    if (!$user instanceof Member) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $payload = json_decode($request->getContent(), true);
    $requiredParams = [
      'current',
      'new'
    ];

    foreach ($requiredParams as $requiredParam) {
      if (!array_key_exists($requiredParam, $payload)) {
        throw new HttpException(Response::HTTP_BAD_REQUEST, "Missing required field: '$requiredParam'");}
    }

    $currentPwd = $payload['current'];
    $newPwd = trim((string) $payload['new']);

    if (!$passwordHasher->isPasswordValid($user, $currentPwd)) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Invalid password");
    }

    // Password validation
    if (empty($newPwd) || strlen($newPwd) < 8) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "New password must be at least 8 letters long");
    }

    $memberRepository->upgradePassword($user, $passwordHasher->hashPassword($user, $newPwd));

    return new JsonResponse();
  }

}
