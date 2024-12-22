<?php

namespace App\Tests\Controller;

use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\Tests\AbstractTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\MemberFactory;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\UserMemberFactory;
use App\Tests\Story\_InitStory;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends AbstractTestCase {
  public function testSuccessful(): void {
    $this->makeAllLoggedRequests(
      null,
      ResponseCodeEnum::ok,
      ResponseCodeEnum::ok,
      ResponseCodeEnum::ok,
      ResponseCodeEnum::ok,
      ResponseCodeEnum::ok,
      ResponseCodeEnum::ok,
      ResponseCodeEnum::ok,
    );
  }

  public function testLoginAsNotActivated(): void {
    // We create a not activated account
    $notActivated = UserFactory::createOne([
      'email' => 'notactivated@user.fr',
      'plainPassword' => 'testuser123',
      'accountActivated' => false,
    ]);

    $this->loggedAs("notactivated@user.fr", "testuser123");
    $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

    // We enable the account
    $this->loggedAsSuperAdmin();
    $iri = $this->getIriFromResource($notActivated);
    $this->makePatchRequest($iri, ['accountActivated' => true]);

    // Account is now activated, the user can log in
    $this->loggedAs("notactivated@user.fr", "testuser123");
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);
  }

  public function testLoginWithWrongPassword(): void {
    $this->loggedAs("admin@admin.com", "wrong");
    $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

    // We request a PRIVATE resource
    $this->makeGetRequest('/clubs');
    $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
  }

  /**
   * Account does not exist
   *
   * @return void
   */
  public function testLoginAsUnknown(): void {
    $this->loggedAs("notexisting@test.fr", "test");
    $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
  }

  public function testLoginWithMultipleProfiles(): void {
    $club1 = _InitStory::club_1();

    $this->loggedAsAdminClub1();
    $response = $this->makeGetRequest('/self');
    $this->assertCount(1, $response->toArray()['linkedProfiles']);

    // No error when getting members
    $this->makeGetRequest($this->getIriFromResource($club1) . '/members');
    $this->assertResponseIsSuccessful();

    // We linked the admin club 1 with another account
    $member = MemberFactory::createOne();
    $userMember = UserMemberFactory::createOne([
      "member" => $member,
      "user" => _InitStory::USER_admin_club_1(),
      "role" => ClubRole::member
    ]);

    $response = $this->makeGetRequest('/self');
    $this->assertCount(2, $response->toArray()['linkedProfiles']);


    $this->makeGetRequest($this->getIriFromResource($club1) . '/members');
    $this->assertResponseStatusCodeSame(ResponseCodeEnum::bad_request->value);
    $this->assertJsonContains([
      "detail" => "Missing required 'Member' header.",
    ]);

    // We try getting as regular member, access should be denied
    $this->selectedMember($member->getUuid()->toString());
    $this->makeGetRequest($this->getIriFromResource($club1) . '/members');
    $this->assertResponseStatusCodeSame(ResponseCodeEnum::forbidden->value);

    // As admin club no problem
    $this->selectedMember(_InitStory::MEMBER_admin_club_1()->getUuid()->toString());
    $this->makeGetRequest($this->getIriFromResource($club1) . '/members');
    $this->assertResponseIsSuccessful();
  }
}
