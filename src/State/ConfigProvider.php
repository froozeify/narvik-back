<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Config;
use App\Entity\Member;
use App\Enum\GlobalSetting;
use App\Enum\MemberRole;
use App\Service\GlobalSettingService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ConfigProvider implements ProviderInterface {

  public function __construct(
    private Security $security,
    private AuthorizationCheckerInterface $authorizationChecker,
    private GlobalSettingService $globalSettingService,
    private ContainerBagInterface $params,
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

    $this->getUserConfig($config);

    return $config;
  }

  public function getDefaultConfig(Config $config): void {
    if ($this->authorizationChecker->isGranted(MemberRole::admin->value) ||
        $this->params->get('app.expose_version')
    ) {
      $config->setAppVersion(\Composer\InstalledVersions::getRootPackage()['pretty_version']);
    }

    $config->setLogo($this->globalSettingService->getSettingValue(GlobalSetting::LOGO));
  }

  public function getUserConfig(Config $config): void {
    $config->setId('user');
  }

}
