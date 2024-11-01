<?php

namespace App\Controller;

use App\Enum\GlobalSetting;
use App\Repository\UserRepository;
use App\Service\GlobalSettingService;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController {

  #[Route(path: ['/auth/bdg/{token}'], name: 'auth_bdg', methods: ['POST'])]
  public function loginBadger(string $token, JWTTokenManagerInterface $JWTTokenManager, RefreshTokenManagerInterface $refreshTokenManager, RefreshTokenGeneratorInterface $refreshTokenGenerator, GlobalSettingService $globalSettingService, UserRepository $userRepository): JsonResponse {

    // We get the db token
    $dbToken = $globalSettingService->getRequiredSettingValue(GlobalSetting::BADGER_TOKEN);

    if ($dbToken !== $token && $this->getParameter("kernel.environment") !== "dev") {
      return new JsonResponse(status: Response::HTTP_NOT_FOUND);
    }

    $badgerUser = $userRepository->findOneBy([
      "email" => "badger"
    ]);

    $refreshToken = $refreshTokenGenerator->createForUserWithTtl($badgerUser, $this->getParameter("gesdinet_jwt_refresh_token.ttl"));
    $refreshTokenManager->save($refreshToken); // We save the generated refresh token

    return new JsonResponse([
      'token' => $JWTTokenManager->create($badgerUser),
      'refresh_token' => $refreshToken->getRefreshToken(),
      'refresh_token_expiration' => $refreshToken->getValid()->getTimestamp()
    ]);
  }
}
