<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\ClubDependent\Plugin\Presence\Activity;
use App\Message\CerberePresencesDateMessage;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Symfony\Component\Messenger\MessageBusInterface;

class ImportCerbereService {
  private array $csvActivities = [];

  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly ActivityRepository $activityRepository,
    private readonly MessageBusInterface $bus,
  ) {
  }

  /**
   * @param string $filename
   * @return int
   *
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
   */
  public function importFromFile(string $filename): int {
    $reader = new Xls();
    $spreadsheet = $reader->load($filename);
    $rows = $spreadsheet->getSheet(0)->toArray();
    $presences = $this->extractFormattedDatesSubArray($rows);

    // We save the activities
    $this->saveCsvActivities();

    foreach ($presences as $k => $presence) {
      /** @var \DateTimeImmutable $date */
      $date = $presence['date'];
      $this->bus->dispatch(new CerberePresencesDateMessage($date, $presence['datas']));
    }

    return count($presences);
  }

  private function saveCsvActivities(): void {
    foreach ($this->csvActivities as $csvActivity) {
      $csvActivity = trim((string) $csvActivity);
      $dbActivity = $this->activityRepository->findOneBy(["name" => $csvActivity]);
      if ($dbActivity) {
        continue;
      }

      $activity = new Activity();
      $activity->setName($csvActivity);
      $this->em->persist($activity);
    }
    $this->em->flush();
  }

  /**
   * @param array $rows
   * @return array{date: \DateTimeImmutable, datas: array{array{licence: string, activities: array{string}} } }
   */
  private function extractFormattedDatesSubArray(array $rows): array {
    $result = [];

    $licenceColHeader = 'N°Licence';
    $activityColHeader = 'Activité';
    $totalPresenceColHeader = 'Nombre de présences:';
    $activitiesColId = null;

    $subArray = [];
    foreach ($rows as $key => $row) {
      if ($row[0] && \DateTime::createFromFormat("d/m/Y", $row[0]) !== false) { // We are in the date row, we continue

        // We are in the copyright line
        if (!empty($row[2])) {
          $result[] = $subArray; // We save the last line
          break;
        }

        if (!empty($subArray)) { // We save it has a dataset
          $result[] = $subArray;
        }

        // We register the new sub-dataset
        $subArray = [
          'date' => \DateTimeImmutable::createFromFormat("d/m/Y", $row[0])->setTime(14, 0, 0),
          'datas' => []
        ];
        continue;
      }

      if ($row[0] === $licenceColHeader) {
        // We register the column id for the activities
        if (!$activitiesColId) {
          foreach ($row as $rowId => $headerName) {
            if ($headerName === $activityColHeader) {
              $activitiesColId = $rowId;
              break;
            }
          }
        }
      } else {
        if (!$activitiesColId) { // We don't have mapped the activities header col
          continue;
        }

        if ($row[0] === $totalPresenceColHeader) {
          continue; // We don't save anymore data in the subset
        }

        $activities = array_filter(explode(";", (string) $row[$activitiesColId]));
        foreach ($activities as &$activity) {
          $activity = trim($activity);
          if (!in_array($activity, $this->csvActivities)) {
            $this->csvActivities[] = $activity;
          }
        }

        $subArray['datas'][$row[0]] = [
          'licence' => $row[0],
          'activities' => $activities,
          'fullName' => $row[1]
        ];
      }
    }

    return $result;
  }

}
