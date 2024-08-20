<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Config;
use App\Entity\Member;
use App\Enum\GlobalSetting;
use App\Enum\MemberRole;
use App\Mailer\EmailService;
use App\Service\GlobalSettingService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ConfigProvider implements ProviderInterface {

  public function __construct(
    private readonly Security $security,
    private readonly AuthorizationCheckerInterface $authorizationChecker,
    private readonly GlobalSettingService $globalSettingService,
    private readonly ContainerBagInterface $params,
    private readonly EmailService $emailService,
  ) {
  }

  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null {
    if ($operation instanceof CollectionOperationInterface) {
      return null;
    }

    $config = new Config();
    $this->getDefaultConfig($config);

    $member = $this->security->getUser();

    if (!$member instanceof Member) {
      return $config;
    }

    $this->getUserConfig($config, $member);

    return $config;
  }

  public function getDefaultConfig(Config $config): void {
    if ($this->authorizationChecker->isGranted(MemberRole::admin->value) ||
        $this->params->get('app.expose_version')
    ) {
      $config->setAppVersion(\Composer\InstalledVersions::getRootPackage()['pretty_version']);
    }

    $config->setLogo($this->globalSettingService->getSettingValue(GlobalSetting::LOGO));

    // Email notifications
    $config->addModule('notifications', [
      'enabled' => $this->emailService->canSendEmail(),
    ]);
  }

  public function getUserConfig(Config $config, Member $member): void {
    $config->setId('user');

    // User a supervisor, he can have access to the sale management
    if ($this->authorizationChecker->isGranted(MemberRole::supervisor->value)) {
      $config->addModule('sales', [
        'enabled' => true,
      ]);
    }
  }

}
