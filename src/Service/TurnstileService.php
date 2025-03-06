<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TurnstileService {

  public function __construct(
    private readonly HttpClientInterface $client,
    private readonly ParameterBagInterface $params,
  ) {
  }

  public function isEnabled(): bool {
    $privateKey = $this->params->get('app.turnstile_secret');
    return !empty($privateKey);
  }

  public function verifyToken(string $token): bool {
    $secret = $this->params->get('app.turnstile_secret');

    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    $response = $this->client->request(
      Request::METHOD_POST,
      $url,
      [
        'json' => [
          'secret' => $secret,
          'response' => $token,
        ]
      ]
    );

    if ($response->getStatusCode() !== Response::HTTP_OK) {
      return false;
    }

    $responseArray = $response->toArray(false);
    if (!array_key_exists("success", $responseArray) || !$responseArray['success']) {
      return false;
    }

    return true;
  }
}
