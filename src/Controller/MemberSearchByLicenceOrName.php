<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Repository\MemberRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class MemberSearchByLicenceOrName extends AbstractController {

  public function __invoke(Request $request, MemberRepository $memberRepository, SerializerInterface $serializer): Response {
    $payload = $this->checkAndGetJsonValues($request, ['query']);
    $query = $payload['query'];

    $members = $memberRepository->findByLicenceOrName($query);

    return new Response($serializer->serialize($members, 'json', [
      'groups' => 'autocomplete'
    ]));
  }

}
