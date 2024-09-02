<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Entity\Member;
use App\Service\MemberService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberSelfUpdatePassword extends AbstractController {

  public function __invoke(Request $request, UserPasswordHasherInterface $passwordHasher, MemberService $memberService): JsonResponse {
    $user = $this->getUser();
    if (!$user instanceof Member) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $payload = $this->checkAndGetJsonValues($request, ['current', 'new']);

    $currentPwd = $payload['current'];
    $newPwd = trim((string) $payload['new']);

    if (!$passwordHasher->isPasswordValid($user, $currentPwd)) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Invalid password");
    }

    $changeError = $memberService->changeMemberPassword($user, $newPwd);
    if ($changeError) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, $changeError);
    }

    return new JsonResponse();
  }

}
