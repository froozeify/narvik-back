<?php

namespace App\Service;

use App\Entity\Club;
use App\Entity\File as FileEntity;
use Symfony\Component\HttpFoundation\File\File as SfFile;
use App\Enum\FileCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileService {

  public function __construct(
    private readonly Filesystem $fs,
    private readonly ContainerBagInterface $params,
    private readonly EntityManagerInterface $entityManager,
  ) {
  }


  private function createFolderIfNotExist(string $path): void {
    if (!$this->fs->exists($path)) {
      mkdir($path, recursive: true);
    }
  }

  public function remove(FileEntity $file): void {
    $filesFolder = $this->params->get('app.files');
    $path = $filesFolder . '/' . $file->getPath();
    if ($this->fs->exists($path)) {
      $this->fs->remove($path);
    }
  }

  public function importFile(SfFile $file, string $filename, FileCategory $fileCategory, bool $isPublic = false, ?Club $club = null, bool $flush = true): FileEntity {
    $filesFolder = $this->params->get('app.files');
    $path = '';

    if ($club && $club->getUuid()) {
      $path .= '/clubs/' . $club->getUuid()->toString() . '/';
    } else {
      $path .= '/generic/';
    }
    $filesFolder .= $path;


    $this->createFolderIfNotExist($filesFolder);
    $fileSavedName = $this->getUniqueFilename($file, $path);
    $extension = $file->getExtension();
    if (!empty($extension)) {
      $fileSavedName .= "." . $file->getExtension();
    }

    $mimeType = $file->getMimeType();

    // We move the file
    $file->move($filesFolder, $fileSavedName);

    $fileEntity = new FileEntity();
    $fileEntity
      ->setPath($path . $fileSavedName)
      ->setFilename($filename)
      ->setCategory($fileCategory)
      ->setMimeType($mimeType)
      ->setisPublic($isPublic)
      ->setClub($club);

    $this->entityManager->persist($fileEntity);

    if ($flush) {
      $this->entityManager->flush();
    }

    return $fileEntity;
  }

  private function getUniqueFilename(SfFile $file, string $path): string {
    $uniqueFilename = UuidService::encodeToReadable(UuidService::generateUuid());
    if ($this->fs->exists($path . $uniqueFilename)) {
      return $this->getUniqueFilename($file, $path);
    }
    return $uniqueFilename;
  }
}
