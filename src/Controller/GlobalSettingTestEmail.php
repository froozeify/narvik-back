<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Mailer\EmailService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GlobalSettingTestEmail extends AbstractController {

  public function __invoke(Request $request, EmailService $emailService) {
    $json = $this->checkAndGetJsonValues($request, ['to']);

    $email = $emailService->getEmail('test.html.twig', 'Configuration SMTP');
    if (!$email) {
      return new JsonResponse();
    }

    $email->to($json['to']);
    $emailService->sendEmail($email);

    return new JsonResponse();
  }

}
