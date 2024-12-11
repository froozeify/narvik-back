<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Entity\User;
use App\Enum\UserRole;
use App\Repository\ClubRepository;
use App\Service\ClubService;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController {

  #[Route(path: ['/auth/bdg'], name: 'auth_bdg', methods: ['POST'])]
  public function loginBadger(Request $request, JWTTokenManagerInterface $JWTTokenManager, RefreshTokenManagerInterface $refreshTokenManager, RefreshTokenGeneratorInterface $refreshTokenGenerator, ClubRepository $clubRepository, ClubService $clubService): JsonResponse {
    $json = $this->checkAndGetJsonValues($request, ['token', 'club']);

    $club = $clubRepository->findOneByUuid($json['club']);

    if (!$club || $club->getBadgerToken() !== $json['token']) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $user = $clubService->getBadger($club);

    $refreshToken = $refreshTokenGenerator->createForUserWithTtl($user, $this->getParameter("gesdinet_jwt_refresh_token.ttl"));
    $refreshTokenManager->save($refreshToken); // We save the generated refresh token

    return new JsonResponse([
      'token' => $JWTTokenManager->create($user),
      'refresh_token' => $refreshToken->getRefreshToken(),
      'refresh_token_expiration' => $refreshToken->getValid()->getTimestamp()
    ]);
  }
}
