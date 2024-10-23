<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Club;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class UserClubExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface {
  private QueryNameGeneratorInterface $queryNameGenerator;
  private QueryBuilder $queryBuilder;
  private string $rootAlias;

  public function __construct(
    private readonly Security $security,
  ) {
  }

  public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void {
    $this->queryBuilder = $queryBuilder;
    $this->queryNameGenerator = $queryNameGenerator;
    $this->addWhere($resourceClass);
  }

  public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void {
    $this->queryBuilder = $queryBuilder;
    $this->queryNameGenerator = $queryNameGenerator;
    $this->addWhere($resourceClass);
  }

  private function throwAccessDenied(): void {
    throw new HttpException(Response::HTTP_FORBIDDEN, "Access to this resource isn't allowed");
  }

  private function getUser(): User {
    $user = $this->security->getUser();
    if (!$user instanceof User) {
      $this->throwAccessDenied();
    }
    return $user;
  }

  private function getUserCurrentClubRole() {
    // TODO: Return the futur UserClub object
    // It should contain a col `role`, that is the current role
  }

  private function checkUserIsClubSupervisor(): void {

  }

  private function checkUserIsClubAdmin(Club $club): void {
    $user = $this->getUser();
  }

  private function addWhere(string $resourceClass): void {
    $this->rootAlias = $this->queryBuilder->getRootAliases()[0];

    $resourceClassName = explode("\\", $resourceClass);
    $resourceClassName = $resourceClassName[count($resourceClassName) - 1];
    $methodName = "restrict" . $resourceClassName . "As"; //TODO: getUserClubCurrentRole
  }
}
