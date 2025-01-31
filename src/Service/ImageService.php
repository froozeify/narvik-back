<?php

namespace App\Service;

use App\Entity\Club;
use App\Entity\File as FileEntity;
use App\Entity\Image;
use App\Enum\FileCategory;
use App\Repository\ClubDependent\MemberRepository;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Example;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File as SfFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\Part\File;


class ImageService {

  public function __construct(
    private readonly Filesystem $fs,
    private readonly ContainerBagInterface $params,
    private readonly FileRepository $fileRepository,
    private readonly MemberRepository $memberRepository,
    private readonly FileService $fileService,
    private readonly EntityManagerInterface $entityManager,
  ) {
  }


  private function createFolderIfNotExist(string $path): void {
    if (!$this->fs->exists($path)) {
      mkdir($path, recursive: true);
    }
  }

  private function removeFolder(string $path): void {
    if ($this->fs->exists($path)) {
      $this->fs->remove($path);
    }
  }

  public function getLogoFile(bool $white = false): ?File {
    $path = $this->params->get('app.public_images');
    if ($white) {
      $path .= '/logo-narvik-white.png';
    } else {
      $path .= '/logo-narvik.png';
    }

    if (!$this->fs->exists($path)) {
      return null;
    }

    return new File($path);
  }

  public function importClubLogo(Club $club, UploadedFile $file): void {
    $clubSettings = $club->getSettings();
    if (!$clubSettings) return;

    // We remove all old profile images
    $oldPictures = $this->fileRepository->findByClubAndCategory($club, FileCategory::logo);
    foreach ($oldPictures as $oldPicture) {
      $this->entityManager->remove($oldPicture);
    }

    $dbFile = $this->fileService->importFile($file, $file->getFilename(), FileCategory::logo, isPublic: true, club: $club, flush: false);

    $clubSettings->setLogo($dbFile);
    $this->entityManager->persist($clubSettings);

    $this->entityManager->flush();
  }

  public function importItacPhotos(Club $club, UploadedFile $file): void {
    // We remove all old profile images
    $oldPictures = $this->fileRepository->findByClubAndCategory($club, FileCategory::member_picture);
    foreach ($oldPictures as $oldPicture) {
      $this->entityManager->remove($oldPicture);
    }

    $fileFolder = $this->params->get('app.files');
    $tmpFolder = $fileFolder . '/tmp_zip_itac_photos_' . UuidService::generateUuid();
    $this->createFolderIfNotExist($tmpFolder);

    // We import from the zip
    $zipArchive = new \ZipArchive();
    $zipArchive->open($file->getRealPath());
    $zipArchive->extractTo($tmpFolder);
    $zipArchive->close();

    $finder = new Finder();
    $finder->files()->in($tmpFolder);
    if (!$finder->hasResults()) {
      return;
    }

    foreach ($finder as $findFile) {
      // We only import for match member
      $licence = explode('.', $findFile->getFilename(), 2)[0];
      if (empty($licence)) {
        continue;
      }
      $member = $this->memberRepository->findOneByLicence($club, $licence);
      if (!$member) {
        continue;
      }

      $uploadedFile = new SfFile($findFile->getRealPath());
      $dbFile = $this->fileService->importFile($uploadedFile, $findFile->getFilename(), FileCategory::member_picture, club: $club, flush: false);

      $member->setProfileImage($dbFile);
      $this->entityManager->persist($member);
    }

    $this->entityManager->flush();
    $this->removeFolder($tmpFolder);
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
