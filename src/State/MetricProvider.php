<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Metric;
use App\Repository\ExternalPresenceRepository;
use App\Repository\Interface\PresenceRepositoryInterface;
use App\Repository\MemberPresenceRepository;
use App\Repository\MemberRepository;
use App\Repository\MemberSeasonRepository;
use Doctrine\ORM\EntityManagerInterface;

class MetricProvider implements ProviderInterface {
  public const METRICS = [
    "members",
    "presences",
    "external-presences",
    "import-batches",
    "activities"
  ];

  public function __construct(
    private readonly MemberRepository $memberRepository,
    private readonly MemberSeasonRepository $memberSeasonRepository,
    private readonly MemberPresenceRepository $memberPresenceRepository,
    private readonly ExternalPresenceRepository $externalPresenceRepository,
    private readonly EntityManagerInterface $entityManager,
  ) {
  }

  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null {
    if ($operation instanceof CollectionOperationInterface) {
      return $this->getAll();
    }

    return $this->getOne($uriVariables['name']);
  }

  private function getAll(): array {
    $metrics = [];
    foreach (self::METRICS as $metric) {
      $metric = $this->getOne($metric);
      if ($metric) {
        $metrics[] = $metric;
      }
    }
    return $metrics;
  }

  private function getOne(string $identifier): ?Metric {
    if (in_array($identifier, self::METRICS)) {
      $getter = "get" . str_replace("-", "", $identifier);
      if (method_exists($this, $getter)) {
        return $this->$getter($identifier);
      }
    }
    return null;
  }

  protected function getMembers(string $identifier): Metric {
    $total = $this->memberRepository->countTotalMembers();
    $previousSeason = $this->memberSeasonRepository->countTotalMembersForPreviousSeason();
    $currentSeason = $this->memberSeasonRepository->countTotalMembersForThisSeason();

    $metric = new Metric();
    $metric->setName($identifier);
    $metric->setValue($total);
    $metric->setChildMetrics([
      (new Metric())
        ->setName("previous-season")
        ->setValue($previousSeason),
      (new Metric())
        ->setName("current-season")
        ->setValue($currentSeason),
    ]);
    return $metric;
  }

  protected function getPresences(string $identifier): Metric {
    return $this->generatePresenceMetrics($identifier, $this->memberPresenceRepository);
  }

  protected function getExternalPresences(string $identifier): Metric {
    return $this->generatePresenceMetrics($identifier, $this->externalPresenceRepository);
  }

  private function generatePresenceMetrics(string $identifier, PresenceRepositoryInterface $repository): Metric {
    $total = $repository->countTotalPresences();

    $currentYear = $repository->countTotalPresencesYearlyUntilToday();
    $currentYearOpenedDays = $repository->countNumberOfPresenceDaysYearlyUntilToday();


    $lastYear = $repository->countTotalPresencesYearlyForPreviousYear();
    $lastYearOpenedDays = $repository->countNumberOfPresenceDaysYearlyForPreviousYear();

    $metric = new Metric();
    $metric->setName($identifier);
    $metric->setValue($total);
    $metric->setChildMetrics([
      (new Metric())
        ->setName("previous-year")
        ->setValue($lastYear)
        ->setChildMetrics([
          (new Metric())
            ->setName("opened-days")
            ->setValue($lastYearOpenedDays),
        ]),
      (new Metric())
        ->setName("current-year")
        ->setValue($currentYear)
        ->setChildMetrics([
          (new Metric())
            ->setName("opened-days")
            ->setValue($currentYearOpenedDays),
        ]),
    ]);
    return $metric;
  }

  protected function getImportBatches(string $identifier): Metric {
    $sql = "SELECT count(m.id) FROM messenger_messages m WHERE m.queue_name = 'csv_import'";

    $res = $this->entityManager->getConnection()->prepare($sql)->executeQuery()->fetchOne();

    $metric = new Metric();
    $metric->setName($identifier);
    $metric->setValue($res);
    return $metric;
  }

  protected function getActivities(string $identifier): Metric {
    $currentYearTotal = $lastYearTotal = 0;
    $currentYearMetrics = $lastYearMetrics = [];
    foreach ($this->memberPresenceRepository->countPresencesPerActivitiesYearlyUntilToday() as $datas) {
      $m = new Metric();
      $m->setName($datas["name"])
        ->setValue($datas["total"]);
      $currentYearTotal += $datas["total"];
      $currentYearMetrics[] = $m;
    }

    foreach ($this->memberPresenceRepository->countPresencesPerActivitiesYearlyForPreviousYear() as $datas) {
      $m = new Metric();
      $m->setName($datas["name"])
        ->setValue($datas["total"]);
      $lastYearTotal += $datas["total"];
      $lastYearMetrics[] = $m;
    }

    $metric = new Metric();
    $metric->setName($identifier)
           ->setValue(0)
           ->setChildMetrics([
             (new Metric())
               ->setName("previous-year")
               ->setValue($lastYearTotal)
               ->setChildMetrics($lastYearMetrics),
             (new Metric())
               ->setName("current-year")
               ->setValue($currentYearTotal)
               ->setChildMetrics($currentYearMetrics),
           ]);

    return $metric;
  }
}
