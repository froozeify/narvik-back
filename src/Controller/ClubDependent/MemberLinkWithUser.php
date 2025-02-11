<?php

namespace App\Controller\ClubDependent;

use App\Controller\Abstract\AbstractClubDependentController;
use App\Entity\ClubDependent\Member;
use App\Entity\UserMember;
use App\Repository\UserMemberRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MemberLinkWithUser extends AbstractClubDependentController {

  public function __invoke(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Member $member, UserService $userService, UserRepository $userRepository, UserMemberRepository $userMemberRepository, EntityManagerInterface $em, ValidatorInterface $validator): Member {
    $payload = $this->checkAndGetJsonValues($request, ['email']);
    $email = $payload['email'];

    $user = $userRepository->findOneByEmail($email);
    if (!$user) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, 'No active account exist with this email.');
    }

    $userMember = $userMemberRepository->findOneByMember($member);
    if ($userMember) {
      if ($userMember->getUser() === $user) {
        return $member; // Nothing change
      } else {
        $userMember->setUser($user); // We update the link with the new user
      }
    } else {
      $userMember = new UserMember();
      $userMember
        ->setUser($user)
        ->setMember($member);
    }

    $errors = $validator->validate($userMember);
    if (count($errors) > 0) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, $errors);
    }

    $em->persist($userMember);
    $em->flush();

    return $member;
  }

}
