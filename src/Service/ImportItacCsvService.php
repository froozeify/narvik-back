<?php

namespace App\Service;

use App\Enum\GlobalSetting;
use App\Message\ItacMembersMessage;
use App\Message\ItacSecondaryClubMembersMessage;
use League\Csv\Reader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\MessageBusInterface;

class ImportItacCsvService {

  public function __construct(
    private readonly MessageBusInterface $bus,
    private readonly GlobalSettingService $globalSettingService,
  ) {
  }

  /**
   * @param string $filename
   * @return int
   * @throws \League\Csv\Exception
   * @throws \League\Csv\UnavailableStream
   */
  public function importFromFile(string $filename): int {
    $reader = Reader::createFromPath($filename);
    $reader->setHeaderOffset(0); // Header is in first line
    $records = $reader->getRecords();
    $array = iterator_to_array($records);
    foreach (array_chunk($array, 100) as $recordsChunk) {
      $chunk = [];
      foreach ($recordsChunk as $key => $value) {
        foreach ($value as $k => $v) {
          $chunk[$key][$this->convert($k)] = $this->convert($v);
        }
      }
      $this->bus->dispatch(new ItacMembersMessage($chunk));
    }

    $this->globalSettingService->updateSettingValue(GlobalSetting::LAST_ITAC_IMPORT, (new \DateTimeImmutable())->format('c'));

    return count($array);
  }

  /**
   * @param string $filename
   * @return int
   * @throws \League\Csv\Exception
   * @throws \League\Csv\UnavailableStream
   */
  public function importSecondaryFromFile(string $filename): int {
    $reader = Reader::createFromPath($filename);
    $reader->setHeaderOffset(0); // Header is in first line
    $records = $reader->getRecords();
    $array = iterator_to_array($records);
    foreach (array_chunk($array, 100) as $recordsChunk) {
      $chunk = [];
      foreach ($recordsChunk as $key => $value) {
        foreach ($value as $k => $v) {
          $chunk[$key][$this->convert($k)] = $this->convert($v);
        }
      }
      $this->bus->dispatch(new ItacSecondaryClubMembersMessage($chunk));
    }

    $this->globalSettingService->updateSettingValue(GlobalSetting::LAST_SECONDARY_CLUB_ITAC_IMPORT, (new \DateTimeImmutable())->format('c'));

    return count($array);
  }

  private function convert(string $string): string {
    $encoding = mb_detect_encoding($string);
    if ($encoding === 'UTF-8') { // Nothing special to do
      return $string;
    } else if ($encoding === 'ASCII') {
      return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
    } else {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Unsupported CSV encoding : '$encoding'");
    }
  }
}
