<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Enum\MemberSecurityCodeTrigger;
use App\Repository\MemberRepository;
use App\Service\MemberService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MemberPasswordReset extends AbstractController {

  public function __invoke(Request $request, MemberRepository $memberRepository, MemberService $memberService): JsonResponse {
    $payload = $this->checkAndGetJsonValues($request, ['email', 'password', 'securityCode']);
    $email = $payload['email'];
    $password = $payload['password'];
    $securityCode = $payload['securityCode'];

    $member = $memberRepository->findOneByEmail($email);
    if (!$member) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $validated = $memberService->validateSecurityCode($member, MemberSecurityCodeTrigger::resetPassword, $securityCode);
    if (!$validated) {
      $memberService->initiateResetPassword($member); // We trigger a new password query
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $changeError = $memberService->changeMemberPassword($member, $password);
    if ($changeError) {
      $memberService->initiateResetPassword($member); // We trigger a new password query
      throw new HttpException(Response::HTTP_BAD_REQUEST, $changeError);
    }

    return new JsonResponse();
  }

}
