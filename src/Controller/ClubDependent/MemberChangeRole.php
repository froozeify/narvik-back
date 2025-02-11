<?php

namespace App\Controller\ClubDependent;

use App\Controller\Abstract\AbstractClubDependentController;
use App\Entity\ClubDependent\Member;
use App\Entity\UserMember;
use App\Enum\ClubRole;
use App\Repository\UserMemberRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MemberChangeRole extends AbstractClubDependentController {

  public function __invoke(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Member $member, UserService $userService, UserMemberRepository $userMemberRepository, EntityManagerInterface $em, ValidatorInterface $validator): Member {
    $payload = $this->checkAndGetJsonValues($request, ['role']);
    $role = ClubRole::tryFrom($payload['role']);

    if (!$member->getEmail()) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Member must have an email address.");
    }

    if (!$role) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Invalid role.");
    }

    $user = $userService->createOrGetFromMember($member);
    if (!$user) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Failed to create the user.");
    }

    $userMember = $userMemberRepository->findOneByMember($member);
    if (!$userMember) {
      $userMember = new UserMember();
      $userMember
        ->setUser($user)
        ->setMember($member);
    }

    $userMember->setRole($role);

    $errors = $validator->validate($userMember);
    if (count($errors) > 0) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, $errors);
    }

    $em->persist($userMember);
    $em->flush();

    return $member;
  }

}
