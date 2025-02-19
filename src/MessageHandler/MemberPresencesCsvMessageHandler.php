<?php

namespace App\MessageHandler;

use App\Importer\ImportMemberPresence;
use App\Message\MemberPresencesCsvMessage;
use App\Repository\ClubRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MemberPresencesCsvMessageHandler {

  public function __construct(
    private readonly ClubRepository $clubRepository,
    private readonly ImportMemberPresence $importMemberPresence,
  ) {
  }


  public function __invoke(MemberPresencesCsvMessage $message): void {
    $club = $this->clubRepository->findOneByUuid($message->getClubUuid());
    if (!$club) return;

    $this->importMemberPresence->setClub($club);
    $this->importMemberPresence->fromArrayWKeys($message->getRecords());
  }
}
