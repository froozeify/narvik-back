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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MemberPhotoService {

  public function __construct(
    private Filesystem $fs,
    private ContainerBagInterface $params,
  ) {
  }


  public function importFromFile(UploadedFile $file): void {
    $imagesFolder = $this->params->get('app.members_photos');
    if (!$this->fs->exists($imagesFolder)) {
      mkdir($imagesFolder, recursive: true);
    }

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
    ];

    $memberImage = $this->params->get('app.members_photos') . "/$licence";
    foreach ($possibleExtensions as $extension) {
      if ($this->fs->exists("$memberImage.$extension")) {
        return "/images/" . base64_encode("members/$licence.$extension");
      }
    }
    return null;
  }

  public function loadImageFromPublicPath(string $publicId): ?Image {
    $path = base64_decode($publicId);
    if (!str_starts_with($path, "members/")) {
      return null;
    }

    $imageFolder = $this->params->get('app.members_photos');
    $filename = substr($path, 8);

    if ($this->fs->exists("$imageFolder/$filename")) {
      $image = new Image();
      $image->setId($publicId)
        ->setName($filename);

      $this->setDataUri("$imageFolder/$filename", $image);

      return $image;
    }
    return null;
  }

  private function setDataUri($imagePath, Image $image): void {
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->file($imagePath);

    $data = "data:$type;base64," . base64_encode(file_get_contents($imagePath));
    $image->setMimeType($type)
      ->setBase64($data);
  }
}
