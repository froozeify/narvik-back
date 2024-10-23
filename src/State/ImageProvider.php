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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ImageProvider implements ProviderInterface {

  public function __construct(
    private readonly ImageService $imageService,
  ) {
  }

  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null {
    if ($operation instanceof CollectionOperationInterface) {
      return null;
    }

    $response = null;

    $isInline = false;
    if (str_starts_with($operation->getName(), 'inline_')) {
      $isInline = true;
    }

    if (str_contains($operation->getName(), 'public_image')) {
      $response = $this->imageService->loadImageFromPublicPath($uriVariables['id'], $isInline);
    } else {
      $response = $this->imageService->loadImageFromProtectedPath($uriVariables['id'], $isInline);
    }

    if ($response && $isInline) {
      // We return the image directly
      return new BinaryFileResponse($response->getPath());
    }

    return $response;
  }


}
