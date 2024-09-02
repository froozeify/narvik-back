<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Repository\MemberRepository;
use App\Service\MemberService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MemberPasswordResetInitiate extends AbstractController {

  public function __invoke(Request $request, MemberRepository $memberRepository, MemberService $memberService): JsonResponse {
    $payload = $this->checkAndGetJsonValues($request, ['email']);
    $email = $payload['email'];

    $member = $memberRepository->findOneByEmail($email);
    if (!$member) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $initialised = $memberService->initiateResetPassword($member);
    if (!$initialised) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    return new JsonResponse();
  }

}
