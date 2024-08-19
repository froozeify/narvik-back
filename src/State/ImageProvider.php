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
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class ImageProvider implements ProviderInterface {

  public function __construct(
    private readonly ImageService $imageService,
  ) {
  }

  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null {
    if ($operation instanceof CollectionOperationInterface) {
      return null;
    }

    if ($operation->getName()) {
      if ($operation->getName() === "public_image") {
        return $this->imageService->loadImageFromPublicPath($uriVariables['id']);
      }
    }

    return $this->imageService->loadImageFromProtectedPath($uriVariables['id']);
  }


}
