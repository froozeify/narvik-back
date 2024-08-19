<?php

namespace App\Service;

use App\Enum\GlobalSetting;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Twig\Environment;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\EventListener\MessageListener;

class EmailService {
  public function __construct(
    private readonly GlobalSettingService $globalSettingService,
    private readonly Environment $twig,
  ) {
  }

  public function canSendEmail(): bool {
    $smtpSetting = $this->globalSettingService->getSettingValue(GlobalSetting::SMTP_ON);
    $smtpSender = $this->globalSettingService->getSettingValue(GlobalSetting::SMTP_SENDER);

    if (empty($smtpSender)) {
      return false;
    }

    if ($smtpSetting) {
      return $this->toBoolean($smtpSetting);
    }
    return false;
  }

  public function getEmail(string $template, string $subject, array $context = []): TemplatedEmail {
    $htmlBody = $this->twig->render('email/' . $template, $context);

    // We load the default sender configuration
    $email = new TemplatedEmail();
    $email
      ->from('tochange@test.fr')
      ->subject($subject)
      ->html($htmlBody)
      ->context($context);

    return $email;
  }

  public function sendEmail(TemplatedEmail $email): void {
    if (!$this->canSendEmail()) {
      return;
    }

    // We load the smtp configuration
    $transport = Transport::fromDsn('smtp://mail:1025');

    // We send the mail TODO: Make it async
    $mailer = new Mailer($transport);
    $mailer->send($email);
  }

  /**
   * Convert the passed value to boolean.
   * Will be `false` if value is incorrect
   *
   * @param $value
   * @return bool
   */
  private function toBoolean($value): bool {
    return is_bool($value) ? $value : !in_array(strtolower((string) $value), ['', '0', 'false']);
  }
}
