<?php

namespace App\Controller;

use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;

class MemberSearchByLicenceOrName extends AbstractController {

  public function __invoke(Request $request, MemberRepository $memberRepository, SerializerInterface $serializer): Response {
    $payload = json_decode($request->getContent(), true);
    $requiredParams = [
      'query'
    ];

    foreach ($requiredParams as $requiredParam) {
      if (!array_key_exists($requiredParam, $payload)) {
        throw new HttpException(Response::HTTP_BAD_REQUEST, "Missing required field: '$requiredParam'");}
    }

    $query = $payload['query'];

    $members = $memberRepository->findByLicenceOrName($query);

    return new Response($serializer->serialize($members, 'json', [
      'groups' => 'autocomplete'
    ]));
  }

}
