<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserSelfDeleteAccount extends AbstractController {

  public function __invoke(EntityManagerInterface $em): JsonResponse {
    $user = $this->getUser();
    if (!$user instanceof User) {
      throw new NotFoundHttpException();
    }

    $em->remove($user);
    $em->flush();

    return new JsonResponse();
  }
}
