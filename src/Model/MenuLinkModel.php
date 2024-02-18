<?php

namespace App\Model;

class MenuLinkModel {
  public function __construct(
    private string $url,
    private string $label,
    private string $icon = '',
    private array $attributes = []
  ) {

  }

  public function getLabel(): string {
    return $this->label;
  }

  public function setLabel(string $label): void {
    $this->label = $label;
  }

  public function getUrl(): string {
    return $this->url;
  }

  public function setUrl(string $url): void {
    $this->url = $url;
  }

  public function getAttributes(): array {
    return $this->attributes;
  }

  public function setAttributes(array $attributes): void {
    $this->attributes = $attributes;
  }

  public function getIcon(): string {
    return $this->icon;
  }

  public function setIcon(string $icon): void {
    $this->icon = $icon;
  }
}
