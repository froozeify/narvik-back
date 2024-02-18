<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ActivityMustBeEnabled extends Constraint {
  public $message = 'The activity "{{ name }}" is not available.';

}
