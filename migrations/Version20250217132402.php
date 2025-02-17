<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217132402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Project v3';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (name VARCHAR(255) NOT NULL, is_enabled BOOLEAN NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC74095AD17F50A6 ON activity (uuid)');
        $this->addSql('CREATE INDEX IDX_AC74095A61190A32 ON activity (club_id)');
        $this->addSql('CREATE TABLE age_category (id INT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE club (name VARCHAR(255) NOT NULL, is_activated BOOLEAN DEFAULT true NOT NULL, renew_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, sales_enabled BOOLEAN DEFAULT false NOT NULL, badger_token VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, contact_name VARCHAR(255) DEFAULT NULL, contact_phone VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8EE3872D17F50A6 ON club (uuid)');
        $this->addSql('CREATE TABLE club_setting (itac_import_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, itac_import_remaining INT DEFAULT 0 NOT NULL, itac_secondary_import_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, itac_secondary_import_remaining INT DEFAULT 0 NOT NULL, cerbere_import_remaining INT DEFAULT 0 NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, club_id INT NOT NULL, logo_id INT DEFAULT NULL, control_shooting_activity_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_923C1D1AD17F50A6 ON club_setting (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_923C1D1A61190A32 ON club_setting (club_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_923C1D1AF98F144A ON club_setting (logo_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_923C1D1AC94ED4A ON club_setting (control_shooting_activity_id)');
        $this->addSql('CREATE TABLE club_setting_exclude_activities_od (club_setting_id INT NOT NULL, activity_id INT NOT NULL, PRIMARY KEY(club_setting_id, activity_id))');
        $this->addSql('CREATE INDEX IDX_9FE426E5A7F3F39F ON club_setting_exclude_activities_od (club_setting_id)');
        $this->addSql('CREATE INDEX IDX_9FE426E581C06096 ON club_setting_exclude_activities_od (activity_id)');
        $this->addSql('CREATE TABLE external_presence (licence VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, date DATE NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_867B7556D17F50A6 ON external_presence (uuid)');
        $this->addSql('CREATE INDEX IDX_867B755661190A32 ON external_presence (club_id)');
        $this->addSql('CREATE TABLE external_presence_activity (external_presence_id INT NOT NULL, activity_id INT NOT NULL, PRIMARY KEY(external_presence_id, activity_id))');
        $this->addSql('CREATE INDEX IDX_41CA98269BEFB2B3 ON external_presence_activity (external_presence_id)');
        $this->addSql('CREATE INDEX IDX_41CA982681C06096 ON external_presence_activity (activity_id)');
        $this->addSql('CREATE TABLE file (category VARCHAR(255) NOT NULL, filename VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, is_public BOOLEAN DEFAULT false NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, club_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8C9F3610D17F50A6 ON file (uuid)');
        $this->addSql('CREATE INDEX IDX_8C9F361061190A32 ON file (club_id)');
        $this->addSql('CREATE TABLE global_setting (id INT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE inventory_category (name VARCHAR(255) NOT NULL, weight INT DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B206A350D17F50A6 ON inventory_category (uuid)');
        $this->addSql('CREATE INDEX IDX_B206A35061190A32 ON inventory_category (club_id)');
        $this->addSql('CREATE TABLE inventory_item (name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, purchase_price NUMERIC(8, 2) DEFAULT NULL, can_be_sold BOOLEAN DEFAULT false NOT NULL, selling_price NUMERIC(8, 2) NOT NULL, selling_quantity INT NOT NULL, quantity INT DEFAULT NULL, quantity_alert INT DEFAULT NULL, barcode VARCHAR(255) DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, category_id INT DEFAULT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_55BDEA30D17F50A6 ON inventory_item (uuid)');
        $this->addSql('CREATE INDEX IDX_55BDEA3012469DE2 ON inventory_item (category_id)');
        $this->addSql('CREATE INDEX IDX_55BDEA3061190A32 ON inventory_item (club_id)');
        $this->addSql('CREATE TABLE inventory_item_history (selling_price NUMERIC(8, 2) DEFAULT NULL, purchase_price NUMERIC(8, 2) DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, item_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_27487B02D17F50A6 ON inventory_item_history (uuid)');
        $this->addSql('CREATE INDEX IDX_27487B02126F525E ON inventory_item_history (item_id)');
        $this->addSql('CREATE TABLE member (email VARCHAR(180) DEFAULT NULL, licence VARCHAR(10) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, gender VARCHAR(1) NOT NULL, birthday DATE DEFAULT NULL, handisport BOOLEAN NOT NULL, postal1 VARCHAR(255) DEFAULT NULL, postal2 VARCHAR(255) DEFAULT NULL, postal3 VARCHAR(255) DEFAULT NULL, zip_code INT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, phone VARCHAR(14) DEFAULT NULL, mobile_phone VARCHAR(14) DEFAULT NULL, blacklisted BOOLEAN NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, profile_image_id INT DEFAULT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA78D17F50A6 ON member (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA78C4CF44DC ON member (profile_image_id)');
        $this->addSql('CREATE INDEX IDX_70E4FA7861190A32 ON member (club_id)');
        $this->addSql('CREATE TABLE member_presence (date DATE NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, member_id INT DEFAULT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9EAA644FD17F50A6 ON member_presence (uuid)');
        $this->addSql('CREATE INDEX IDX_9EAA644F7597D3FE ON member_presence (member_id)');
        $this->addSql('CREATE INDEX IDX_9EAA644F61190A32 ON member_presence (club_id)');
        $this->addSql('CREATE TABLE member_presence_activity (member_presence_id INT NOT NULL, activity_id INT NOT NULL, PRIMARY KEY(member_presence_id, activity_id))');
        $this->addSql('CREATE INDEX IDX_1389D5BE2A15EB06 ON member_presence_activity (member_presence_id)');
        $this->addSql('CREATE INDEX IDX_1389D5BE81C06096 ON member_presence_activity (activity_id)');
        $this->addSql('CREATE TABLE member_season (is_secondary_club BOOLEAN DEFAULT false NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, member_id INT NOT NULL, season_id INT NOT NULL, age_category_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D432D99AD17F50A6 ON member_season (uuid)');
        $this->addSql('CREATE INDEX IDX_D432D99A7597D3FE ON member_season (member_id)');
        $this->addSql('CREATE INDEX IDX_D432D99A4EC001D1 ON member_season (season_id)');
        $this->addSql('CREATE INDEX IDX_D432D99AE1F4383B ON member_season (age_category_id)');
        $this->addSql('CREATE TABLE sale (price NUMERIC(8, 2) NOT NULL, comment VARCHAR(255) DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, seller_id INT DEFAULT NULL, payment_mode_id INT DEFAULT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E54BC005D17F50A6 ON sale (uuid)');
        $this->addSql('CREATE INDEX IDX_E54BC0058DE820D9 ON sale (seller_id)');
        $this->addSql('CREATE INDEX IDX_E54BC0056EAC8BA0 ON sale (payment_mode_id)');
        $this->addSql('CREATE INDEX IDX_E54BC00561190A32 ON sale (club_id)');
        $this->addSql('CREATE TABLE sale_payment_mode (name VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, available BOOLEAN NOT NULL, weight INT DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_853B81EBD17F50A6 ON sale_payment_mode (uuid)');
        $this->addSql('CREATE INDEX IDX_853B81EB61190A32 ON sale_payment_mode (club_id)');
        $this->addSql('CREATE TABLE sale_purchased_item (item_name VARCHAR(255) DEFAULT NULL, item_category VARCHAR(255) DEFAULT NULL, item_price NUMERIC(8, 2) NOT NULL, quantity INT NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, item_id INT DEFAULT NULL, sale_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F0E80A6D17F50A6 ON sale_purchased_item (uuid)');
        $this->addSql('CREATE INDEX IDX_5F0E80A6126F525E ON sale_purchased_item (item_id)');
        $this->addSql('CREATE INDEX IDX_5F0E80A64A7E4868 ON sale_purchased_item (sale_id)');
        $this->addSql('CREATE TABLE season (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, account_activated BOOLEAN NOT NULL, email VARCHAR(180) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON "user" (uuid)');
        $this->addSql('CREATE TABLE user_member (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, role VARCHAR(255) NOT NULL, user_id INT NOT NULL, member_id INT DEFAULT NULL, badger_club_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_15B6E145A76ED395 ON user_member (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_15B6E1457597D3FE ON user_member (member_id)');
        $this->addSql('CREATE INDEX IDX_15B6E145A43EF515 ON user_member (badger_club_id)');
        $this->addSql('CREATE TABLE user_security_code (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, code VARCHAR(10) NOT NULL, trigger VARCHAR(255) NOT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, user_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_468A1109A76ED395 ON user_security_code (user_id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A61190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE club_setting ADD CONSTRAINT FK_923C1D1A61190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE club_setting ADD CONSTRAINT FK_923C1D1AF98F144A FOREIGN KEY (logo_id) REFERENCES file (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE club_setting ADD CONSTRAINT FK_923C1D1AC94ED4A FOREIGN KEY (control_shooting_activity_id) REFERENCES activity (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE club_setting_exclude_activities_od ADD CONSTRAINT FK_9FE426E5A7F3F39F FOREIGN KEY (club_setting_id) REFERENCES club_setting (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE club_setting_exclude_activities_od ADD CONSTRAINT FK_9FE426E581C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE external_presence ADD CONSTRAINT FK_867B755661190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE external_presence_activity ADD CONSTRAINT FK_41CA98269BEFB2B3 FOREIGN KEY (external_presence_id) REFERENCES external_presence (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE external_presence_activity ADD CONSTRAINT FK_41CA982681C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F361061190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inventory_category ADD CONSTRAINT FK_B206A35061190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inventory_item ADD CONSTRAINT FK_55BDEA3012469DE2 FOREIGN KEY (category_id) REFERENCES inventory_category (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inventory_item ADD CONSTRAINT FK_55BDEA3061190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inventory_item_history ADD CONSTRAINT FK_27487B02126F525E FOREIGN KEY (item_id) REFERENCES inventory_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78C4CF44DC FOREIGN KEY (profile_image_id) REFERENCES file (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA7861190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_presence ADD CONSTRAINT FK_9EAA644F7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_presence ADD CONSTRAINT FK_9EAA644F61190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_presence_activity ADD CONSTRAINT FK_1389D5BE2A15EB06 FOREIGN KEY (member_presence_id) REFERENCES member_presence (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_presence_activity ADD CONSTRAINT FK_1389D5BE81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_season ADD CONSTRAINT FK_D432D99A7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_season ADD CONSTRAINT FK_D432D99A4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_season ADD CONSTRAINT FK_D432D99AE1F4383B FOREIGN KEY (age_category_id) REFERENCES age_category (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT FK_E54BC0058DE820D9 FOREIGN KEY (seller_id) REFERENCES member (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT FK_E54BC0056EAC8BA0 FOREIGN KEY (payment_mode_id) REFERENCES sale_payment_mode (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT FK_E54BC00561190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale_payment_mode ADD CONSTRAINT FK_853B81EB61190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale_purchased_item ADD CONSTRAINT FK_5F0E80A6126F525E FOREIGN KEY (item_id) REFERENCES inventory_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale_purchased_item ADD CONSTRAINT FK_5F0E80A64A7E4868 FOREIGN KEY (sale_id) REFERENCES sale (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_member ADD CONSTRAINT FK_15B6E145A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_member ADD CONSTRAINT FK_15B6E1457597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_member ADD CONSTRAINT FK_15B6E145A43EF515 FOREIGN KEY (badger_club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_security_code ADD CONSTRAINT FK_468A1109A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095A61190A32');
        $this->addSql('ALTER TABLE club_setting DROP CONSTRAINT FK_923C1D1A61190A32');
        $this->addSql('ALTER TABLE club_setting DROP CONSTRAINT FK_923C1D1AF98F144A');
        $this->addSql('ALTER TABLE club_setting DROP CONSTRAINT FK_923C1D1AC94ED4A');
        $this->addSql('ALTER TABLE club_setting_exclude_activities_od DROP CONSTRAINT FK_9FE426E5A7F3F39F');
        $this->addSql('ALTER TABLE club_setting_exclude_activities_od DROP CONSTRAINT FK_9FE426E581C06096');
        $this->addSql('ALTER TABLE external_presence DROP CONSTRAINT FK_867B755661190A32');
        $this->addSql('ALTER TABLE external_presence_activity DROP CONSTRAINT FK_41CA98269BEFB2B3');
        $this->addSql('ALTER TABLE external_presence_activity DROP CONSTRAINT FK_41CA982681C06096');
        $this->addSql('ALTER TABLE file DROP CONSTRAINT FK_8C9F361061190A32');
        $this->addSql('ALTER TABLE inventory_category DROP CONSTRAINT FK_B206A35061190A32');
        $this->addSql('ALTER TABLE inventory_item DROP CONSTRAINT FK_55BDEA3012469DE2');
        $this->addSql('ALTER TABLE inventory_item DROP CONSTRAINT FK_55BDEA3061190A32');
        $this->addSql('ALTER TABLE inventory_item_history DROP CONSTRAINT FK_27487B02126F525E');
        $this->addSql('ALTER TABLE member DROP CONSTRAINT FK_70E4FA78C4CF44DC');
        $this->addSql('ALTER TABLE member DROP CONSTRAINT FK_70E4FA7861190A32');
        $this->addSql('ALTER TABLE member_presence DROP CONSTRAINT FK_9EAA644F7597D3FE');
        $this->addSql('ALTER TABLE member_presence DROP CONSTRAINT FK_9EAA644F61190A32');
        $this->addSql('ALTER TABLE member_presence_activity DROP CONSTRAINT FK_1389D5BE2A15EB06');
        $this->addSql('ALTER TABLE member_presence_activity DROP CONSTRAINT FK_1389D5BE81C06096');
        $this->addSql('ALTER TABLE member_season DROP CONSTRAINT FK_D432D99A7597D3FE');
        $this->addSql('ALTER TABLE member_season DROP CONSTRAINT FK_D432D99A4EC001D1');
        $this->addSql('ALTER TABLE member_season DROP CONSTRAINT FK_D432D99AE1F4383B');
        $this->addSql('ALTER TABLE sale DROP CONSTRAINT FK_E54BC0058DE820D9');
        $this->addSql('ALTER TABLE sale DROP CONSTRAINT FK_E54BC0056EAC8BA0');
        $this->addSql('ALTER TABLE sale DROP CONSTRAINT FK_E54BC00561190A32');
        $this->addSql('ALTER TABLE sale_payment_mode DROP CONSTRAINT FK_853B81EB61190A32');
        $this->addSql('ALTER TABLE sale_purchased_item DROP CONSTRAINT FK_5F0E80A6126F525E');
        $this->addSql('ALTER TABLE sale_purchased_item DROP CONSTRAINT FK_5F0E80A64A7E4868');
        $this->addSql('ALTER TABLE user_member DROP CONSTRAINT FK_15B6E145A76ED395');
        $this->addSql('ALTER TABLE user_member DROP CONSTRAINT FK_15B6E1457597D3FE');
        $this->addSql('ALTER TABLE user_member DROP CONSTRAINT FK_15B6E145A43EF515');
        $this->addSql('ALTER TABLE user_security_code DROP CONSTRAINT FK_468A1109A76ED395');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE age_category');
        $this->addSql('DROP TABLE club');
        $this->addSql('DROP TABLE club_setting');
        $this->addSql('DROP TABLE club_setting_exclude_activities_od');
        $this->addSql('DROP TABLE external_presence');
        $this->addSql('DROP TABLE external_presence_activity');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE global_setting');
        $this->addSql('DROP TABLE inventory_category');
        $this->addSql('DROP TABLE inventory_item');
        $this->addSql('DROP TABLE inventory_item_history');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE member_presence');
        $this->addSql('DROP TABLE member_presence_activity');
        $this->addSql('DROP TABLE member_season');
        $this->addSql('DROP TABLE sale');
        $this->addSql('DROP TABLE sale_payment_mode');
        $this->addSql('DROP TABLE sale_purchased_item');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_member');
        $this->addSql('DROP TABLE user_security_code');
    }
}
