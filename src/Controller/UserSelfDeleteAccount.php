<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserSelfDeleteAccount extends AbstractController {

  public function __invoke(EntityManagerInterface $em): JsonResponse {
    $user = $this->getUser();
    if (!$user instanceof User) {
      throw new NotFoundHttpException();
    }

    if ($user->getRole() === UserRole::super_admin) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "You can't delete this account.");
    }

    $em->remove($user);
    $em->flush();

    return new JsonResponse();
  }
}
