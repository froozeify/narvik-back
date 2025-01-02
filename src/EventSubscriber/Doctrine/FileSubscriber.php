<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\File;
use App\Service\FileService;
use App\Service\UuidService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;

#[AsEntityListener(entity: File::class)]
class FileSubscriber extends AbstractEventSubscriber {
  public function __construct(
    private readonly FileService $fileService,
  ) {
  }


  public function postLoad(File $file, PostLoadEventArgs $args): void {
    $fileId = UuidService::encodeToReadable($file->getUuid());

    // In the future we could manage different url for text files
    if ($file->getIsPublic()) {
      $file->setPublicUrl("/public/images/$fileId");
      $file->setPublicInlineUrl("/public/images/inline/$fileId");
    }

    $file->setPrivateUrl("/images/$fileId");
  }

  public function postRemove(File $file, PostRemoveEventArgs $args): void {
    $this->fileService->remove($file);
  }
}
