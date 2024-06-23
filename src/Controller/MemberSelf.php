<?php

namespace App\Controller;

use App\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MemberSelf extends AbstractController {

  public function __invoke(): Member {
    $user = $this->getUser();
    if (!$user instanceof Member) {
      throw new NotFoundHttpException();
    }
    return $user;
  }

}
