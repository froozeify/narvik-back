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
    private Filesystem $fs,
    private ContainerBagInterface $params,
  ) {
  }

  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null {
    if ($operation instanceof CollectionOperationInterface) {
      return null;
    }

    if ($operation->getName()) {
      if ($operation->getName() === "public_image") {
        return $this->loadImageFromPublicPath($uriVariables['id']);
      }
    }

    return $this->loadImageFromProtectedPath($uriVariables['id']);
  }

  private function loadImageFromPath(string $publicId, string $path): ?Image {
    if ($this->fs->exists($path)) {
      $filename = explode("/", $path);
      $filename = end($filename);

      $image = new Image();
      $image->setId($publicId)
            ->setName($filename);

      $this->setDataUri($path, $image);

      return $image;
    }
    return null;
  }

  private function loadImageFromProtectedPath(string $publicId): ?Image {
    $path = base64_decode($publicId);

    if (str_contains('./', $path)) {
      return null;
    }

    // Image accessible to everyone logged
    $imageFolder = $this->params->get('app.images');
    return $this->loadImageFromPath($publicId, "$imageFolder/$path");
  }

  private function loadImageFromPublicPath(string $publicId): ?Image {
    $path = base64_decode($publicId);

    // Public resource is at root
    if (str_contains('/', $path)) {
      return null;
    }

    $imageFolder = $this->params->get('app.public_image');
    return $this->loadImageFromPath($publicId, "$imageFolder/$path");
  }

  private function setDataUri($imagePath, Image $image): void {
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->file($imagePath);

    $data = "data:$type;base64," . base64_encode(file_get_contents($imagePath));
    $image->setMimeType($type)
          ->setBase64($data);
  }
}
