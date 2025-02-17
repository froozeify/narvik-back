<?php

namespace App\Serializer;

use ApiPlatform\State\SerializerContextBuilderInterface;
use App\Enum\ClubRole;
use App\Enum\UserRole;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final readonly class DefaultContextBuilder implements SerializerContextBuilderInterface {
  public function __construct(
    private SerializerContextBuilderInterface $decorated,
    private AuthorizationCheckerInterface $authorizationChecker,
  ) {
  }

  public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array {
    $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
    $resourceClass = $context['resource_class'] ?? null;

    if (!array_key_exists('groups', $context)) {
      // No groups defined, no need to add our custom ones
      return $context;
    }

    if (is_string($context['groups'])) {
      $context['groups'] = [$context['groups']];
    }

    // Normalization context
    if ($normalization) {
      $context['groups'][] = 'common-read';
      $context['groups'][] = 'timestamp';
      if ($this->authorizationChecker->isGranted(ClubRole::supervisor->value, $request)) {
        $context['groups'][] = 'club-supervisor-read';
      }
      if ($this->authorizationChecker->isGranted(ClubRole::admin->value, $request)) {
        $context['groups'][] = 'club-admin-read';
      }
      if ($this->authorizationChecker->isGranted(UserRole::super_admin->value, $request)) {
        $context['groups'][] = 'super-admin-read';
      }
    } else {
      // FIXME: Context are not well applied
      // i.e: When POST /activities $request do not contain the club ref
      $context['groups'][] = 'common-write';
      if ($this->authorizationChecker->isGranted(ClubRole::supervisor->value, $request)) {
        $context['groups'][] = 'club-supervisor-write';
      }
      if ($this->authorizationChecker->isGranted(ClubRole::admin->value, $request)) {
        $context['groups'][] = 'club-admin-write';
      }
      if ($this->authorizationChecker->isGranted(UserRole::super_admin->value, $request)) {
        $context['groups'][] = 'super-admin-write';
      }
    }

    return $context;
  }
}
