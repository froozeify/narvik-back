<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Entity\MemberSecurityCode;
use App\Enum\MemberSecurityCodeTrigger;
use App\Mailer\EmailService;
use App\Repository\MemberRepository;
use App\Repository\MemberSecurityCodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MemberPasswordResetInitiate extends AbstractController {

  public function __invoke(Request $request, MemberRepository $memberRepository, MemberSecurityCodeRepository $memberSecurityCodeRepository, EntityManagerInterface $em, EmailService $emailService): JsonResponse {
    if (!$emailService->canSendEmail()) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $payload = $this->checkAndGetJsonValues($request, ['email']);
    $email = $payload['email'];

    $member = $memberRepository->findOneByEmail($email);
    if (!$member) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    // We verify that we don't have more than 4 in progress reset for this user
    $resetInProgress = $memberSecurityCodeRepository->findAllByTrigger($member, MemberSecurityCodeTrigger::resetPassword);
    if (count($resetInProgress) > 3) {
      throw new HttpException(Response::HTTP_TOO_MANY_REQUESTS);
    }

    $securityCode = new MemberSecurityCode();
    $securityCode->setTrigger(MemberSecurityCodeTrigger::resetPassword)->setMember($member);

    $em->persist($securityCode);
    $em->flush();

    // We sent the security code
    $email = $emailService->getEmail('security-code.html.twig', 'Changement de mot de passe', ['security_code' => $securityCode->getCode()]);
    $emailService->sendEmail($email, $member->getEmail());

    return new JsonResponse();
  }

}
