<?php

namespace App\EventSubscriber\Kernel;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

final class LoginSubscriber implements EventSubscriberInterface {
  private Request $request;

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => [
        ['requestEvent', 10],
      ],
      Events::AUTHENTICATION_SUCCESS => [
        ['jwtSuccess', 10],
      ],
    ];
  }

  public function __construct(
    private RateLimiterFactory $memberIpLoginLimiter,
    private RateLimiterFactory $ipLoginLimiter,
  ) {
  }


  public function requestEvent(RequestEvent $event): void {
    $route = $event->getRequest()->attributes->get('_route');
    if ($route !== 'api_login') {
      return;
    }

    $this->request = $event->getRequest();
    $ipLimiter = $this->ipLoginLimiter->create($event->getRequest()->getClientIp());
    $ipLimiter->consume(1)->ensureAccepted();

    $memberLimiter = $this->getMemberLimiter();
    $memberLimiter?->consume(1)->ensureAccepted();
  }

  public function jwtSuccess(AuthenticationSuccessEvent $event): void {
    $memberLimiter = $this->getMemberLimiter();
    $memberLimiter?->reset();
  }

  private function getMemberLimiter(): ?LimiterInterface {
    $content = $this->request->getContent();
    if (!$content) {
      return null;
    }

    $json = json_decode($content, true);
    if (!array_key_exists('email', $json)) {
      return null;
    }

    return $this->memberIpLoginLimiter->create("{$json['email']}/{$this->request->getClientIp()}");
  }
}
