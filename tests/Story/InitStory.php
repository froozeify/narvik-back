<?php

namespace App\Tests\Story;

use App\Entity\Club;
use App\Entity\User;
use App\Enum\ClubRole;
use App\Factory\ClubFactory;
use App\Factory\MemberFactory;
use App\Factory\MemberSeasonFactory;
use App\Factory\UserFactory;
use App\Factory\UserMemberFactory;
use Zenstruck\Foundry\Story;
use function Zenstruck\Foundry\faker;

/**
 * @method static User super_admin()
 * @method static User admin_club_1() This user is also a member of club 2 with just member role
 * @method static User admin_club_2()
 * @method static User supervisor_club_1()
 * @method static User member_club_1()
 * @method static Club club_1()
 * @method static Club club_2()
 */
final class InitStory extends Story {
  public function build(): void {
    $this->addState('super_admin', UserFactory::new()->superAdmin("admin@admin.com")->create(), 'super_admin');

    $this->addState('club_1', ClubFactory::createOne([
        'name' => 'Club 1',
        'salesEnabled' => true,
        'smtpEnabled' => true,
        'badgerToken' => 'club1longbadgertoken',
      ]), 'clubs');
    $this->addState('club_2', ClubFactory::createOne([
      'name' => 'Club 2',
      'salesEnabled' => false,
      'smtpEnabled' => false,
    ]), 'clubs');

    $this->addToPool('clubs', ClubFactory::createMany(faker()->numberBetween(2, 5)));

    // We create a one club admin and supervisor for each
    $this->addState('admin_club_1', UserFactory::createOne([
      'email' => 'admin@club1.fr',
      'plainPassword' => 'admin123',
      'accountActivated' => true,
    ]), 'users');
    $this->addState('admin_club_2', UserFactory::createOne([
      'email' => 'admin@club2.fr',
      'plainPassword' => 'admin123',
      'accountActivated' => true,
    ]), 'users');
    $this->addState('supervisor_club_1', UserFactory::createOne([
      'email' => 'supervisor@club1.fr',
      'plainPassword' => 'admin123',
      'accountActivated' => true,
    ]), 'users');
    $this->addState('member_club_1', UserFactory::createOne([
      'email' => 'member@club1.fr',
      'plainPassword' => 'member123',
      'accountActivated' => true,
    ]), 'users');

    // We create the association club <-> Member <-> User
    $adminMember = MemberFactory::createOne([
      'club' => self::club_1(),
      'email' => 'admin@club1.fr',
    ]);
    $adminMember2 = MemberFactory::createOne([
      'club' => self::club_2(),
      'email' => 'admin@club2.fr',
    ]);
    $supervisorMember = MemberFactory::createOne([
      'club' => self::club_1(),
      'email' => 'supervisor@club1.fr',
    ]);
    $memberUser = MemberFactory::createOne([
      'club' => self::club_1(),
      'email' => 'member@club1.fr',
    ]);
    $memberUser2 = MemberFactory::createOne([
      'club' => self::club_2(),
      'email' => 'admin@club1.fr',
    ]);

    UserMemberFactory::createOne([
      'member' => $adminMember,
      'user' => self::admin_club_1(),
      'role' => ClubRole::admin,
    ]);
    UserMemberFactory::createOne([
      'member' => $adminMember2,
      'user' => self::admin_club_2(),
      'role' => ClubRole::admin,
    ]);
    UserMemberFactory::createOne([
      'member' => $supervisorMember,
      'user' => self::supervisor_club_1(),
      'role' => ClubRole::supervisor,
    ]);
    UserMemberFactory::createOne([
      'member' => $memberUser,
      'user' => self::member_club_1(),
      'role' => ClubRole::member,
    ]);
    UserMemberFactory::createOne([
      'member' => $memberUser2,
      'user' => self::admin_club_1(),
      'role' => ClubRole::member,
    ]);

    $users = UserFactory::createMany(faker()->numberBetween(5, 10));

    // We create some member
    $members = MemberFactory::createMany(faker()->numberBetween(30, 40), [
      'memberSeasons' => MemberSeasonFactory::new()->many(0, 4),
    ]);

    // We link the member with user
    foreach ($members as $member) {
      UserMemberFactory::createOne([
        'user' => faker()->randomElement($users),
        'member' => $member,
      ]);
    }
  }
}
