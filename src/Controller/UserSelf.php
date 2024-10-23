<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
