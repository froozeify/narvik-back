<?php

namespace App\Serializer;

use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Yaml\Parser;


class CsvSerializer implements NormalizerAwareInterface, NormalizerInterface {
  use NormalizerAwareTrait;

  private const ALREADY_CALLED_NORMALIZER = "CSV_NORMALIZER_ALREADY_CALLED";

  public function __construct(
    private KernelInterface $kernel
  ) {
  }


  public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool {
    // Make sure we don't call it twice
    if (isset($context[self::ALREADY_CALLED_NORMALIZER])) return false;

    // We only work when user is impersonated
    return $format === "csv";
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

    // We load custom configs
    $csvConfigs = $this->getCustomCsvConfigs();

    // We don't have a matching custom config, we return the default normalize one
    $class = $context["resource_class"];
    if (!array_key_exists($class, $csvConfigs)) return $data;

    $csvConfig = $csvConfigs[$class];


    if ($context["operation"] instanceof GetCollection) {
      foreach ($data as &$item) {
        $this->updateNormalizedItem($csvConfig, $item);
      }
    } else {
      $this->updateNormalizedItem($csvConfig, $data);
    }

    return $data;
  }

  public function getCustomCsvConfigs(): array {
    $configs = [];
    // We load the files
    $finder = new Finder();
    $finder->files()
           ->in($this->kernel->getProjectDir() . "/config/normalizer_csv")
           ->name(["*.yml", "*.yaml"]);

    $yamlParser = new Parser();
    /** @var SplFileInfo $file */
    foreach ($finder as $file) {
      $configs = array_merge($configs, $yamlParser->parseFile($file->getPathname()));
    }
    return $configs;
  }

  /**
   * @param array $csvConfig
   * @param array $item
   */
  private function updateNormalizedItem(array $csvConfig, array &$item): void {
    // Check if recursive array
    // Sometimes we could be in a sequential state
    if ($this->isSequentialArray($item)) {
      foreach ($item as &$itemSequential) {
        $this->updateNormalizedItem($csvConfig, $itemSequential);
      }
      return;
    }


    foreach ($item as $name => $value) {
      // Remove undeclared fields
      if (!array_key_exists($name, $csvConfig)) {
        unset($item[$name]);
        continue;
      }

      $itemConfig = $csvConfig[$name];
      // If the value is a not an array in the config we know we can apply the new name
      if (!is_array($itemConfig)) {
        if (is_null($itemConfig)) {
          continue;
        } else { // We have to rename the field
          $this->renameKey($item, $name, $itemConfig);

        }
      } else { // We are in an array configuration, we'll have a recursive call
        if (array_key_exists("prefix", $itemConfig)) {
          $this->renameKey($item, $name, $itemConfig["prefix"]);
          $itemNewKey = $itemConfig["prefix"];

          $newFields = $item[$itemNewKey]; // We copy

          $this->updateNormalizedItem($csvConfig[$name]["fields"], $newFields);
          $item[$itemNewKey] = $newFields;
        } else {
          $newFields = $item[$name]; // We copy
          unset($item[$name]); // We remove the field, since we don't want prefixing (will be put at the parent level)

          $this->updateNormalizedItem($csvConfig[$name]["fields"], $newFields);

          $item = array_merge($item, $newFields);
        }
      }
    }
  }

  private function renameKey(&$array, $oldKey, $newKey) {
    $keys = array_keys($array);
    $index = array_search($oldKey, $keys, true);
    $keys[$index] = $newKey;
    $array = array_combine($keys, array_values($array));
  }

  private function isSequentialArray(array $array): bool {
    return !(array_keys($array) !== range(0, count($array) - 1));
  }
}
