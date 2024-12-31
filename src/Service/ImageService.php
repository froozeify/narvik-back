<?php

namespace App\Service;

use App\Entity\Club;
use App\Entity\File as FileEntity;
use App\Entity\Image;
use App\Enum\FileCategory;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\HttpFoundation\File\File as SfFile;


class ImageService {

  public function __construct(
    private readonly Filesystem $fs,
    private readonly ContainerBagInterface $params,
    private readonly FileRepository $fileRepository,
    private readonly FileService $fileService,
    private readonly EntityManagerInterface $entityManager,
  ) {
  }


  private function createFolderIfNotExist(string $path): void {
    if (!$this->fs->exists($path)) {
      mkdir($path, recursive: true);
    }
  }

  public function getLogo(): ?Image {
    //FIXME: Deprecated
    $publicFolder = $this->params->get('app.files');
    return $this->loadImageFromFile('logo.png', "$publicFolder/logo.png", true);
  }

  public function getLogoFile(): ?File {
    //FIXME: Deprecated
    $logo = $this->getLogo();
    if (!$logo) {
      return null;
    }
    return new File($logo->getPath());
  }

  public function importLogo(UploadedFile $file): string {
    //FIXME: Deprecated
    $publicFolder = $this->params->get('app.files');
    $this->createFolderIfNotExist($publicFolder);

    $file->move($publicFolder, "logo.png");
    return bin2hex("logo.png");
  }

  public function importItacPhotos(Club $club, UploadedFile $file): void {
    // We remove all old profile images
    $oldPictures = $this->fileRepository->findByClubAndCategory($club, FileCategory::member_picture);
    foreach ($oldPictures as $oldPicture) {
      $this->fileService->remove($oldPicture);
    }

    // We import from the zip
    $zipArchive = new \ZipArchive();
    $zipArchive->open($file->getRealPath());
    for ($i = 0; $zipFile = $zipArchive->statIndex($i); $i++) {
      if (\is_dir($zipFile['name'])) {
        continue;
      }

      // file contents
      $content = $zipArchive->getFromIndex($i);
      $imageRaw = imagecreatefromstring($content);

      if (!$imageRaw) {
        continue;
      }

      $fileFolder = $this->params->get('app.files');
      $tmpFile = $fileFolder . '/' . UuidService::generateUuid() . '_' . $zipFile['name'] . '.webp';
      imagewebp($imageRaw, $tmpFile);
      $uploadedFile = new SfFile($tmpFile);
      $this->fileService->importFile($uploadedFile, $zipFile['name'], FileCategory::member_picture, club: $club, flush: false);

      // We unset the tmp one
      imagedestroy($imageRaw);
    }
    $zipArchive->close();

    $this->entityManager->flush();
  }

  public function loadImageFromProtectedPath(string $publicId, bool $isInline = false): ?Image {
    $uuid = $this->decodeEncodedUriId($publicId);
    $file = $this->fileRepository->findOneByUuid($uuid->toString());
    if (!$file instanceof FileEntity) {
      return null;
    }

    return $this->loadImageFromFile($file, $isInline);
  }

  public function loadImageFromPublicPath(string $publicId, bool $isInline = false): ?Image {
    $uuid = $this->decodeEncodedUriId($publicId);
    $file = $this->fileRepository->findOneByUuid($uuid->toString());
    if (!$file instanceof FileEntity || !$file->getIsPublic()) {
      return null;
    }

    return $this->loadImageFromFile($file, $isInline);
  }

  private function loadImageFromFile(FileEntity $file, bool $isInline = false): ?Image {
    $imageFolder = $this->params->get('app.files');
    $path = "$imageFolder/{$file->getPath()}";

    if ($this->fs->exists($path)) {
      $image = new Image();
      $image->setId(UuidService::encodeToReadable($file->getUuid()))
            ->setName($file->getFilename())
            ->setPath($path);

      if (!$isInline) {
        $this->setDataUri($path, $image);
      }

      return $image;
    }
    return null;
  }

  private function decodeEncodedUriId(string $encodedId): UuidInterface {
    return UuidService::fromReadable($encodedId);
  }

  private function setDataUri($imagePath, Image $image): void {
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->file($imagePath);

    $data = "data:$type;base64," . base64_encode(file_get_contents($imagePath));
    $image->setMimeType($type)
          ->setBase64($data);
  }
}
