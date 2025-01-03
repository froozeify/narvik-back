<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\ClubDependent\Member;
use App\Entity\Config;
use App\Entity\User;
use App\Enum\ClubRole;
use App\Enum\GlobalSetting;
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

    $user = $this->security->getUser();

    if (!$user instanceof User) {
      return $config;
    }

    $this->getUserConfig($config, $user);

    return $config;
  }

  public function getDefaultConfig(Config $config): void {
    if ($this->authorizationChecker->isGranted(ClubRole::admin->value) ||
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

  public function getUserConfig(Config $config, User $user): void {
    $config->setId('user');
    if ($this->authorizationChecker->isGranted(ClubRole::supervisor->value) || $this->authorizationChecker->isGranted(ClubRole::badger->value)) {
      $config->addModule('presences', [
        'enabled' => true,
      ]);
    }

    // User a supervisor, he can have access to the sale management
    if ($this->authorizationChecker->isGranted(ClubRole::supervisor->value)) {
      $config->addModule('sales', [
        'enabled' => true,
      ]);
    }
  }

}
