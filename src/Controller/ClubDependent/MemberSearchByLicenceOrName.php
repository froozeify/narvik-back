<?php

namespace App\Controller\ClubDependent;

use App\Controller\Abstract\AbstractClubDependentController;
use App\Repository\ClubDependent\MemberRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class MemberSearchByLicenceOrName extends AbstractClubDependentController {

  public function __invoke(Request $request, MemberRepository $memberRepository, SerializerInterface $serializer): Response {
    $payload = $this->checkAndGetJsonValues($request, ['query']);
    $query = $payload['query'];

    $members = $memberRepository->findByLicenceOrName($this->getQueryClub(), $query);

    return new Response($serializer->serialize($members, 'json', [
      'groups' => ['autocomplete', 'common-read']
    ]));
  }
}
