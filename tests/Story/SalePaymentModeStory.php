<?php

namespace App\Tests\Story;

use App\Tests\Factory\SalePaymentModeFactory;
use Zenstruck\Foundry\Story;

final class SalePaymentModeStory extends Story {
  public const array PAYMENTS = [
    'Espèces' => 'banknotes',
    'Chèque' => 'ticket',
    'Carte' => 'credit-card'
  ];

  public function build(): void {
    foreach (self::PAYMENTS as $name => $icon) {
      $this->addToPool('default', SalePaymentModeFactory::createOne([
        'name' => $name,
        'icon' => $icon
      ]));
    }
  }
}
