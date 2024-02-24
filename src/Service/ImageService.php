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

class ImageService {

  public function __construct(
    private Filesystem $fs,
    private ContainerBagInterface $params,
  ) {
  }


  private function createFolderIfNotExist(string $path): void {
    if (!$this->fs->exists($path)) {
      mkdir($path, recursive: true);
    }
  }

  public function importLogo(UploadedFile $file): string {
    $publicFolder = $this->params->get('app.public_image');
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
      'JPEG'
    ];

    $memberImage = $this->params->get('app.members_photos') . "/$licence";
    foreach ($possibleExtensions as $extension) {
      if ($this->fs->exists("$memberImage.$extension")) {
        return "/images/" . bin2hex("members/$licence.$extension");
      }
    }
    return null;
  }
}
