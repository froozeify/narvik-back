<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Image;
use App\Entity\Metric;
use App\Repository\MemberPresenceRepository;
use App\Repository\MemberRepository;
use App\Repository\MemberSeasonRepository;
use App\Service\MemberPhotoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ImageProvider implements ProviderInterface {

  public function __construct(
    private MemberPhotoService $memberPhotoService,
  ) {
  }

  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null {
    if ($operation instanceof CollectionOperationInterface) {
      return null;
    }

    return $this->loadImage($uriVariables['id']);
  }

  private function loadImage(string $encodedImageId): ?Image {
    return $this->memberPhotoService->loadImageFromPublicPath($encodedImageId);
  }
}
