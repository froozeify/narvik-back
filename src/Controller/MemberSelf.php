<?php

namespace App\Controller;

use App\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MemberSelf extends AbstractController {

  public function __invoke(): ?Member {
    $user = $this->getUser();
    if ($user instanceof Member) return $user;
    return null;
  }

}
