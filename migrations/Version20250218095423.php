<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218095423 extends AbstractMigration {
  public function getDescription(): string {
    return 'Add missing CREATE SEQUENCES';
  }

  public function up(Schema $schema): void {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('CREATE SEQUENCE activity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE age_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE club_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE club_setting_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE external_presence_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE file_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE global_setting_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE inventory_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE inventory_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE inventory_item_history_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE member_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE member_presence_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE member_season_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE sale_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE sale_payment_mode_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE sale_purchased_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE season_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
  }

  public function down(Schema $schema): void {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql('DROP SEQUENCE activity_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE age_category_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE club_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE club_setting_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE external_presence_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE file_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE global_setting_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE inventory_category_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE inventory_item_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE inventory_item_history_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE member_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE member_presence_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE member_season_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE sale_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE sale_payment_mode_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE sale_purchased_item_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE season_id_seq CASCADE');
    $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
  }
}
