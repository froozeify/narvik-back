<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserSelf extends AbstractController {

  public function __invoke(): User {
    $user = $this->getUser();
    if (!$user instanceof User) {
      throw new NotFoundHttpException();
    }
    return $user;
  }

}
