<?php

namespace App\EventSubscriber\Kernel;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Interface\ClubLinkedEntityInterface;
use App\Service\RequestService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ClubLinkedEntitySubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => [
        ['requestEvent', EventPriorities::POST_DESERIALIZE],
      ],
    ];
  }

  public function __construct(
    private readonly RequestService $requestService,
  ) {
  }


  public function requestEvent(RequestEvent $event): void {
    if ($event->getRequest()->getMethod() !== Request::METHOD_POST) {
      return;
    }

    // We auto set the club uuid for any ClubLinkedEntity to the uri set one
    $clubLinkedObject = $event->getRequest()->attributes->get('data');
    if (!$clubLinkedObject instanceof ClubLinkedEntityInterface) {
      return;
    }

    $clubRequest = $this->requestService->getClubFromRequest($event->getRequest());
    if (!$clubRequest) {
      return;
    }

    // We update to the matched one
    $clubLinkedObject->setClub($clubRequest);
    $event->getRequest()->attributes->set('data', $clubLinkedObject);
  }

}
