<?php

namespace App\Serializer;

use ApiPlatform\State\SerializerContextBuilderInterface;
use App\Enum\MemberRole;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final readonly class DefaultContextBuilder implements SerializerContextBuilderInterface {
  public function __construct(
    private SerializerContextBuilderInterface $decorated,
    private AuthorizationCheckerInterface $authorizationChecker,
  ) {
  }

  public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array {
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
      if ($this->authorizationChecker->isGranted(MemberRole::admin->value)) {
        $context['groups'][] = 'admin-read';
      }
    } else {
      if ($this->authorizationChecker->isGranted(MemberRole::admin->value)) {
        $context['groups'][] = 'admin-write';
      }
    }

    return $context;
  }
}
