<?php

namespace App\Serializer;

use ApiPlatform\Metadata\GetCollection;
use App\Service\CsvService;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CsvSerializer implements NormalizerAwareInterface, NormalizerInterface {
  use NormalizerAwareTrait;

  private const string ALREADY_CALLED_NORMALIZER = "CSV_NORMALIZER_ALREADY_CALLED";

  public function __construct(
    private readonly KernelInterface $kernel,
    private readonly CsvService $csvService,
  ) {
  }


  public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool {
    if ($format !== "csv") {
      return false;
    }

    // Make sure we don't call it twice
    if (isset($context[self::ALREADY_CALLED_NORMALIZER])) return false;

    $csvConfigs = $this->csvService->getCustomCsvConfigs();

    if (!array_key_exists("resource_class", $context)) {
      return false;
    }
    $class = $context["resource_class"];

    // We don't have a custom CSV mapping, we let the generic api-platform handle it
    if (!array_key_exists($class, $csvConfigs)) return false;

    return true;
  }

  public function getSupportedTypes(?string $format): array {
    return [
      '*' => false,
    ];
  }

  /**
   * READ
   */
  public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null {
    // We mark it to avoid any further call
    $context[self::ALREADY_CALLED_NORMALIZER] = true;

    $data = $this->normalizer->normalize($object, $format, $context);

    $class = $context["resource_class"];

    if ($context["operation"] instanceof GetCollection) {
      foreach ($data as &$item) {
        $this->csvService->normalize($class, $item);
      }
    } else {
      $this->csvService->normalize($class, $data);
    }

    return $data;
  }
}
