<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserProcessor implements ProcessorInterface {
  public function __construct(
    #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
    private readonly ProcessorInterface $persistProcessor,
    #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
    private readonly ProcessorInterface $removeProcessor,
    private readonly AuthorizationCheckerInterface $authorizationChecker,
    private readonly UserPasswordHasherInterface $passwordHasher
  ) {
  }

  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User|null {
    if ($operation instanceof DeleteOperationInterface) {
      if (in_array(UserRole::super_admin->value, $data->getRoles())) {
        throw new HttpException(Response::HTTP_FORBIDDEN, "You can't delete an administrator account");
      }

      return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
    }

    // Custom input, for the moment not supported
    if (!$data instanceof User) {
      return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

    // Admin can change the password
    if ($this->authorizationChecker->isGranted(UserRole::super_admin->value)) {
      if ($data->getPlainPassword()) {
        // Admin can't change other admin
        if (in_array(UserRole::super_admin->value, $data->getRoles())) {
          throw new HttpException(Response::HTTP_FORBIDDEN, "You can't change the password of an administrator");
        }
        $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPlainPassword()));
      }
    }

    return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
  }
}
