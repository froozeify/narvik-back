<?php

namespace App\EventSubscriber\Kernel;

use App\Entity\ClubDependent\Member;
use App\Entity\User;
use League\Bundle\OAuth2ServerBundle\Event\UserResolveEvent;
use League\Bundle\OAuth2ServerBundle\OAuth2Events;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class LoginSubscriber implements EventSubscriberInterface {

  private ?Member $member = null;

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => [
        ['requestEvent', 10],
      ],
      OAuth2Events::USER_RESOLVE => [
        ['onUserResolve', 10],
      ]
//      Events::AUTHENTICATION_SUCCESS => [
//        ['jwtSuccess', 10],
//      ],
    ];
  }

  public function __construct(
    private readonly RequestStack $requestStack,
    private readonly RateLimiterFactory $memberIpLoginLimiter,
    private readonly RateLimiterFactory $ipLoginLimiter,
    private readonly ContainerBagInterface $containerBag,
    private readonly UserProviderInterface $userProvider,
    private readonly UserPasswordHasherInterface $userPasswordHasher,
  ) {
  }


  public function requestEvent(RequestEvent $event): void {
    $route = $event->getRequest()->attributes->get('_route');

    // Test env, we disable the rate limiting
    // All that could be simplified in a future version by not exposing the login
    // OAuth with only login form from API and app use Authorization Code Grant
    if ($this->containerBag->get('kernel.environment') === 'test') {
      return;
    }

    if ($route !== 'api_login') {
      return;
    }

    $ip = $event->getRequest()->getClientIp();
    if (!$ip) {
      return;
    }

    // $ipLimiter = $this->ipLoginLimiter->create($ip);
    // $ipLimiter->consume(1)->ensureAccepted();

    $memberLimiter = $this->getMemberLimiter();
    $memberLimiter?->consume(1)->ensureAccepted();
  }

  public function onUserResolve(UserResolveEvent $event): void {
    try {
      $user = $this->userProvider->loadUserByIdentifier($event->getUsername());
    } catch (AuthenticationException $e) {
      return;
    }

    if (!$user instanceof User) {
      return;
    }

    if (!$this->userPasswordHasher->isPasswordValid($user, $event->getPassword())) {
      return;
    }

    $event->setUser($user);
  }

//  public function jwtSuccess(AuthenticationSuccessEvent $event): void {
//    $user = $event->getUser();
//    if (!$user instanceof Member) {
//      return;
//    }
//
//    $this->member = $user;
//    $memberLimiter = $this->getMemberLimiter();
//    $memberLimiter?->reset();
//  }


  private function getMemberLimiter(): ?LimiterInterface {
    $request = $this->requestStack->getCurrentRequest();
    $email = $this->member?->getEmail();

    if (!$email) {
      // We try getting the email from the request
      $content = $request->getContent();
      if (!$content) {
        return null;
      }

      $json = json_decode($content, true);
      if (!array_key_exists('email', $json)) {
        return null;
      }
      $email = $json['email'];
    }

    return $this->memberIpLoginLimiter->create("{$email}/{$request->getClientIp()}");
  }
}
