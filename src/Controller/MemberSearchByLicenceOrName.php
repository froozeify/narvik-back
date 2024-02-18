<?php

namespace App\Controller;

use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MemberSearchByLicenceOrName extends AbstractController {

  public function __invoke(string $id, MemberRepository $memberRepository): ?array {
    return $memberRepository->findByLicenceOrName($id);
  }

}
