<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Club;
use App\Entity\ClubDependent\Metric;
use App\Repository\ClubDependent\MemberRepository;
use App\Repository\ClubDependent\MemberSeasonRepository;
use App\Repository\ClubDependent\Plugin\Presence\ExternalPresenceRepository;
use App\Repository\ClubDependent\Plugin\Presence\MemberPresenceRepository;
use App\Repository\Interface\PresenceRepositoryInterface;
use App\Service\RequestService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MetricProvider implements ProviderInterface {
  public const METRICS = [
    "members",
    "presences",
    "external-presences",
//    "import-batches",
    "activities"
  ];

  // TODO: super admin only metrics (import-batches)

  private ?Club $club = null;

  public function __construct(
    private readonly RequestStack $requestStack,
    private readonly RequestService $requestService,

    private readonly MemberRepository $memberRepository,
    private readonly MemberSeasonRepository $memberSeasonRepository,
    private readonly MemberPresenceRepository $memberPresenceRepository,
    private readonly ExternalPresenceRepository $externalPresenceRepository,
    private readonly EntityManagerInterface $entityManager,
  ) {
  }

  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null {
    $this->club = null;
    if (str_starts_with($operation->getName(), 'club_metric')) {
      $this->club = $this->requestService->getClubFromRequest($this->requestStack->getCurrentRequest());
    }

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
    $total = $this->memberRepository->countTotalClubMembers($this->club);
    $previousSeason = $this->memberSeasonRepository->countTotalMembersForPreviousSeason($this->club);
    $currentSeason = $this->memberSeasonRepository->countTotalMembersForThisSeason($this->club);

    $metric = new Metric();
    $metric->setClub($this->club);
    $metric->setName($identifier);
    $metric->setValue($total);
    $metric->setChildMetrics([
      (new Metric())
        ->setClub($this->club)
        ->setName("previous-season")
        ->setValue($previousSeason),
      (new Metric())
        ->setClub($this->club)
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
    $total = $repository->countTotalPresences($this->club);

    $currentYear = $repository->countTotalPresencesYearlyUntilToday($this->club);
    $currentYearOpenedDays = $repository->countNumberOfPresenceDaysYearlyUntilToday($this->club);


    $lastYear = $repository->countTotalPresencesYearlyForPreviousYear($this->club);
    $lastYearOpenedDays = $repository->countNumberOfPresenceDaysYearlyForPreviousYear($this->club);

    $metric = new Metric();
    $metric->setClub($this->club);
    $metric->setName($identifier);
    $metric->setValue($total);
    $metric->setChildMetrics([
      (new Metric())
        ->setClub($this->club)
        ->setName("previous-year")
        ->setValue($lastYear)
        ->setChildMetrics([
          (new Metric())
            ->setClub($this->club)
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
    $sql = "SELECT count(m.id) FROM messenger_messages m WHERE m.queue_name IN ('medium', 'low')";

    $res = $this->entityManager->getConnection()->prepare($sql)->executeQuery()->fetchOne();

    $metric = new Metric();
    $metric->setName($identifier);
    $metric->setValue($res);
    return $metric;
  }

  protected function getActivities(string $identifier): Metric {
    $currentYearTotal = $lastYearTotal = 0;
    $currentYearMetrics = $lastYearMetrics = [];
    foreach ($this->memberPresenceRepository->countPresencesPerActivitiesYearlyUntilToday($this->club) as $datas) {
      $m = new Metric();
      $m
        ->setClub($this->club)
        ->setName($datas["name"])
        ->setValue($datas["total"]);
      $currentYearTotal += $datas["total"];
      $currentYearMetrics[] = $m;
    }

    foreach ($this->memberPresenceRepository->countPresencesPerActivitiesYearlyForPreviousYear($this->club) as $datas) {
      $m = new Metric();
      $m
        ->setClub($this->club)
        ->setName($datas["name"])
        ->setValue($datas["total"]);
      $lastYearTotal += $datas["total"];
      $lastYearMetrics[] = $m;
    }

    $metric = new Metric();
    $metric->setName($identifier)
           ->setClub($this->club)
           ->setValue(0)
           ->setChildMetrics([
             (new Metric())
               ->setClub($this->club)
               ->setName("previous-year")
               ->setValue($lastYearTotal)
               ->setChildMetrics($lastYearMetrics),
             (new Metric())
               ->setClub($this->club)
               ->setName("current-year")
               ->setValue($currentYearTotal)
               ->setChildMetrics($currentYearMetrics),
           ]);

    return $metric;
  }
}
