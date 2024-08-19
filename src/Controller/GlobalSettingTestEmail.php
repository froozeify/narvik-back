<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Enum\GlobalSetting;
use App\Service\EmailService;
use App\Service\GlobalSettingService;
use App\Service\ImageService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GlobalSettingTestEmail extends AbstractController {

  public function __invoke(Request $request, EmailService $emailService) {
    $json = $this->checkAndGetJsonValues($request, ['to']);

    $email = $emailService->getEmail('test.html.twig', 'Test configuration SMTP');
    if (!$email) {
      return new JsonResponse();
    }

    $email->to($json['to']);
    $emailService->sendEmail($email);

    return new JsonResponse();
  }

}
