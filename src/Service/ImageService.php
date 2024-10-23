<?php

namespace App\Service;

use App\Entity\AgeCategory;
use App\Entity\Image;
use App\Entity\Member;
use App\Entity\Season;
use App\Enum\ItacCsvHeaderMapping;
use App\Enum\MemberRole;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImageService {

  public function __construct(
    private readonly Filesystem $fs,
    private readonly ContainerBagInterface $params,
  ) {
  }


  private function createFolderIfNotExist(string $path): void {
    if (!$this->fs->exists($path)) {
      mkdir($path, recursive: true);
    }
  }

  public function getLogo(): ?Image {
    $publicFolder = $this->params->get('app.public_images');
    return $this->loadImageFromPath('logo.png', "$publicFolder/logo.png", true);
  }

  public function getLogoFile(): ?File {
    $logo = $this->getLogo();
    if (!$logo) {
      return null;
    }
    return new File($logo->getPath());
  }

  public function importLogo(UploadedFile $file): string {
    $publicFolder = $this->params->get('app.public_images');
    $this->createFolderIfNotExist($publicFolder);

    $file->move($publicFolder, "logo.png");
    return bin2hex("logo.png");
  }

  public function importItacPhotos(UploadedFile $file): void {
    $imagesFolder = $this->params->get('app.members_photos');
    $this->createFolderIfNotExist($imagesFolder);

    $zipArchive = new \ZipArchive();
    $zipArchive->open($file->getRealPath());
    $zipArchive->extractTo($imagesFolder);
  }

  /**
   * Return the member asset public image path
   *
   * @param string $licence
   * @return string|null
   */
  public function getMemberPhotoPath(string $licence): ?string {
    $possibleExtensions = [
      'jpg',
      'JPG',
      'jpeg',
      'JPEG',
    ];

    $memberImage = $this->params->get('app.members_photos') . "/$licence";
    foreach ($possibleExtensions as $extension) {
      if ($this->fs->exists("$memberImage.$extension")) {
        return bin2hex("members/$licence.$extension");
      }
    }
    return null;
  }

  public function loadImageFromProtectedPath(string $publicId, bool $isInline = false): ?Image {
    $path = $this->decodeEncodedUriId($publicId);

    if (!$path || str_contains('./', $path)) {
      return null;
    }

    // Image accessible to everyone logged
    $imageFolder = $this->params->get('app.images');
    return $this->loadImageFromPath($publicId, "$imageFolder/$path", $isInline);
  }

  public function loadImageFromPublicPath(string $publicId, bool $isInline = false): ?Image {
    $path = $this->decodeEncodedUriId($publicId);

    // Public resource is at root
    if (!$path || str_contains('/', $path)) {
      return null;
    }

    $imageFolder = $this->params->get('app.public_images');
    return $this->loadImageFromPath($publicId, "$imageFolder/$path", $isInline);
  }

  private function loadImageFromPath(string $publicId, string $path, bool $isInline = false): ?Image {
    if ($this->fs->exists($path)) {
      $filename = explode("/", $path);
      $filename = end($filename);

      $image = new Image();
      $image->setId($publicId)
            ->setName($filename)
            ->setPath($path);

      if (!$isInline) {
        $this->setDataUri($path, $image);
      }

      return $image;
    }
    return null;
  }

  private function decodeEncodedUriId(string $encodedId): ?string {
    if (!ctype_xdigit($encodedId)) return null;

    return hex2bin($encodedId);
  }

  private function setDataUri($imagePath, Image $image): void {
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->file($imagePath);

    $data = "data:$type;base64," . base64_encode(file_get_contents($imagePath));
    $image->setMimeType($type)
          ->setBase64($data);
  }
}
