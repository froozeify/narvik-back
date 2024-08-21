<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Enum\GlobalSetting;
use App\Mailer\EmailService;
use App\Service\GlobalSettingService;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

class GlobalSettingSmtp extends AbstractController {

  public function __invoke(Request $request, KernelInterface $kernel, GlobalSettingService $globalSettingService, EmailService $emailService) {
    $json = $this->checkAndGetJsonValues($request, ['on', 'host', 'port', 'username', 'password', 'sender', 'senderName']);

    // We apply the settings
    $globalSettingService->updateSettingValue(GlobalSetting::SMTP_ON, $this->toBoolean($json['on']) ? '1' : '0');
    $globalSettingService->updateSettingValue(GlobalSetting::SMTP_HOST, $json['host']);
    $globalSettingService->updateSettingValue(GlobalSetting::SMTP_PORT, (string) $json['port']);
    $globalSettingService->updateSettingValue(GlobalSetting::SMTP_USERNAME, !empty($json['username']) ? $json['username'] : null);
    $globalSettingService->updateSettingValue(GlobalSetting::SMTP_PASSWORD, !empty($json['password']) ? $json['password'] : null);
    $globalSettingService->updateSettingValue(GlobalSetting::SMTP_SENDER, $json['sender']);
    $globalSettingService->updateSettingValue(GlobalSetting::SMTP_SENDER_NAME, !empty($json['senderName']) ? $json['senderName'] : 'Narvik');

    // We restart the messenger so the cache is refreshed
    $application = new Application($kernel);
    $command = new ArrayInput([
      'command' => 'messenger:stop-workers',
    ]);
    $application->doRun($command, new NullOutput());

    $email = $emailService->getEmail('smtp-test.html.twig', 'Configuration SMTP');
    if (!$email || !$this->getMember()) {
      return new JsonResponse();
    }

    $email->to($this->getMember()->getEmail());
    $emailService->sendEmail($email);

    return new JsonResponse();
  }

}
