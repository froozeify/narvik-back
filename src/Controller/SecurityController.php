<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Repository\ClubRepository;
use App\Service\ClubService;
use App\Service\UuidService;
use League\Bundle\OAuth2ServerBundle\Entity\Scope;
use League\Bundle\OAuth2ServerBundle\Manager\Doctrine\RefreshTokenManager;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use Nyholm\Psr7\Response as Psr7Response;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController {

  #[Route(path: ['/auth/bdg'], name: 'auth_bdg', methods: ['POST'])]
  public function loginBadger(
    Request $request,
    KernelInterface $kernel,
    ClientRepositoryInterface $clientRepository,
    AccessTokenRepositoryInterface $accessTokenRepository,
    RefreshTokenRepositoryInterface $refreshTokenRepository,
    ClubRepository $clubRepository,
    ClubService $clubService
  ): Response {
    $json = $this->checkAndGetJsonValues($request, ['token', 'club']);

    $club = $clubRepository->findOneByUuid($json['club']);

    if (!$club || $club->getBadgerToken() !== $json['token']) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $user = $clubService->getBadger($club);
    if (!$user) {
      throw new HttpException(Response::HTTP_BAD_REQUEST);
    }

    $client = $clientRepository->getClientEntity('badger');
    if (!$client) {
      throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    $scope = new Scope();
    $scope->setIdentifier('badger');

    $key = str_replace('%kernel.project_dir%', $kernel->getProjectDir(), $_ENV['OAUTH_PRIVATE_KEY']);
    $cryptKey = new CryptKey($key, $_ENV['OAUTH_PASSPHRASE'], false);

    $accessToken = $accessTokenRepository->getNewToken($client, [$scope], "badger@{$club->getUuid()}");
    $accessToken->setExpiryDateTime((new \DateTimeImmutable())->add(new \DateInterval('PT1H')));
    $accessToken->setIdentifier("badger-" . UuidService::generateUuid()->toString());
    $accessToken->setPrivateKey($cryptKey);

    try {
      $accessTokenRepository->persistNewAccessToken($accessToken);
    }
    catch (UniqueTokenIdentifierConstraintViolationException $e) {
      throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    $refreshToken = $refreshTokenRepository->getNewRefreshToken();
    $refreshToken->setIdentifier($accessToken->getIdentifier());
    $refreshToken->setAccessToken($accessToken);
    $refreshToken->setExpiryDateTime((new \DateTimeImmutable())->add(new \DateInterval('P1M')));

    try {
      $refreshTokenRepository->persistNewRefreshToken($refreshToken);
    } catch (UniqueTokenIdentifierConstraintViolationException $e) {
      throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    $bearer = new BearerTokenResponse();
    $bearer->setPrivateKey($cryptKey);
    $bearer->setEncryptionKey($_ENV['OAUTH_ENCRYPTION_KEY']);
    $bearer->setAccessToken($accessToken);
    $bearer->setRefreshToken($refreshToken);
    $response = $bearer->generateHttpResponse(new Psr7Response());

    $httpFoundationFactory = new HttpFoundationFactory();
    return $httpFoundationFactory->createResponse($response);
  }
}
