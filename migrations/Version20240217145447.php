<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240217145447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE activity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE age_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE external_presence_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE global_setting_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE member_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE member_presence_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE member_season_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE refresh_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE season_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE activity (id INT NOT NULL, name VARCHAR(255) NOT NULL, is_enabled BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE age_category (id INT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE external_presence (id INT NOT NULL, licence VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, date DATE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN external_presence.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN external_presence.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE external_presence_activity (external_presence_id INT NOT NULL, activity_id INT NOT NULL, PRIMARY KEY(external_presence_id, activity_id))');
        $this->addSql('CREATE INDEX IDX_41CA98269BEFB2B3 ON external_presence_activity (external_presence_id)');
        $this->addSql('CREATE INDEX IDX_41CA982681C06096 ON external_presence_activity (activity_id)');
        $this->addSql('CREATE TABLE global_setting (id INT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE member (id INT NOT NULL, roles JSON NOT NULL, role VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, account_activated BOOLEAN NOT NULL, email VARCHAR(180) DEFAULT NULL, licence VARCHAR(10) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, gender VARCHAR(1) NOT NULL, birthday DATE DEFAULT NULL, handisport BOOLEAN NOT NULL, deceased BOOLEAN NOT NULL, postal1 VARCHAR(255) DEFAULT NULL, postal2 VARCHAR(255) DEFAULT NULL, postal3 VARCHAR(255) DEFAULT NULL, zip_code INT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, phone VARCHAR(14) DEFAULT NULL, mobile_phone VARCHAR(14) DEFAULT NULL, blacklisted BOOLEAN NOT NULL, licence_state VARCHAR(255) DEFAULT NULL, licence_type VARCHAR(1) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA781DAAE648 ON member (licence)');
        $this->addSql('CREATE TABLE member_presence (id INT NOT NULL, member_id INT NOT NULL, date DATE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9EAA644F7597D3FE ON member_presence (member_id)');
        $this->addSql('COMMENT ON COLUMN member_presence.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN member_presence.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE member_presence_activity (member_presence_id INT NOT NULL, activity_id INT NOT NULL, PRIMARY KEY(member_presence_id, activity_id))');
        $this->addSql('CREATE INDEX IDX_1389D5BE2A15EB06 ON member_presence_activity (member_presence_id)');
        $this->addSql('CREATE INDEX IDX_1389D5BE81C06096 ON member_presence_activity (activity_id)');
        $this->addSql('CREATE TABLE member_season (id INT NOT NULL, member_id INT DEFAULT NULL, season_id INT DEFAULT NULL, age_category_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D432D99A7597D3FE ON member_season (member_id)');
        $this->addSql('CREATE INDEX IDX_D432D99A4EC001D1 ON member_season (season_id)');
        $this->addSql('CREATE INDEX IDX_D432D99AE1F4383B ON member_season (age_category_id)');
        $this->addSql('CREATE TABLE refresh_tokens (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token)');
        $this->addSql('CREATE TABLE season (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE external_presence_activity ADD CONSTRAINT FK_41CA98269BEFB2B3 FOREIGN KEY (external_presence_id) REFERENCES external_presence (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE external_presence_activity ADD CONSTRAINT FK_41CA982681C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_presence ADD CONSTRAINT FK_9EAA644F7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_presence_activity ADD CONSTRAINT FK_1389D5BE2A15EB06 FOREIGN KEY (member_presence_id) REFERENCES member_presence (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_presence_activity ADD CONSTRAINT FK_1389D5BE81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_season ADD CONSTRAINT FK_D432D99A7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_season ADD CONSTRAINT FK_D432D99A4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_season ADD CONSTRAINT FK_D432D99AE1F4383B FOREIGN KEY (age_category_id) REFERENCES age_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE activity_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE age_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE external_presence_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE global_setting_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE member_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE member_presence_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE member_season_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE refresh_tokens_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE season_id_seq CASCADE');
        $this->addSql('ALTER TABLE external_presence_activity DROP CONSTRAINT FK_41CA98269BEFB2B3');
        $this->addSql('ALTER TABLE external_presence_activity DROP CONSTRAINT FK_41CA982681C06096');
        $this->addSql('ALTER TABLE member_presence DROP CONSTRAINT FK_9EAA644F7597D3FE');
        $this->addSql('ALTER TABLE member_presence_activity DROP CONSTRAINT FK_1389D5BE2A15EB06');
        $this->addSql('ALTER TABLE member_presence_activity DROP CONSTRAINT FK_1389D5BE81C06096');
        $this->addSql('ALTER TABLE member_season DROP CONSTRAINT FK_D432D99A7597D3FE');
        $this->addSql('ALTER TABLE member_season DROP CONSTRAINT FK_D432D99A4EC001D1');
        $this->addSql('ALTER TABLE member_season DROP CONSTRAINT FK_D432D99AE1F4383B');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE age_category');
        $this->addSql('DROP TABLE external_presence');
        $this->addSql('DROP TABLE external_presence_activity');
        $this->addSql('DROP TABLE global_setting');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE member_presence');
        $this->addSql('DROP TABLE member_presence_activity');
        $this->addSql('DROP TABLE member_season');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
