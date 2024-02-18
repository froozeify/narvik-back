<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Member;
use App\Enum\MemberRole;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MemberProcessor implements ProcessorInterface {
  public function __construct(
    #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
    private ProcessorInterface $persistProcessor,
    #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
    private ProcessorInterface $removeProcessor,
    private AuthorizationCheckerInterface $authorizationChecker,
    private UserPasswordHasherInterface $passwordHasher
  ) {
  }

  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Member|null {
    if ($operation instanceof DeleteOperationInterface) {
      return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
    }

    // Custom input, for the moment not supported
    if (!$data instanceof Member) {
      return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

    // Can't deactivate another admin
    if (in_array(MemberRole::admin->value, $data->getRoles())) {
      if (!$data->isAccountActivated()) {
        throw new HttpException(Response::HTTP_FORBIDDEN, "You can't deactivate an administrator account");
      }
    }

    // Admin can change the password (no requirements for super admin)
    if ($this->authorizationChecker->isGranted(MemberRole::admin->value)) {
      if ($data->getPlainPassword()) {
        // Admin can't change other admin
        if (in_array(MemberRole::admin->value, $data->getRoles())) {
          throw new HttpException(Response::HTTP_FORBIDDEN, "You can't change the password of an administrator");
        }
        $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPlainPassword()));
      }
    }

    return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
  }
}
