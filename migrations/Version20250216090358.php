<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216090358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE oauth2_client (name VARCHAR(128) NOT NULL, secret VARCHAR(128) DEFAULT NULL, redirect_uris TEXT DEFAULT NULL, grants TEXT DEFAULT NULL, scopes TEXT DEFAULT NULL, active BOOLEAN NOT NULL, allow_plain_text_pkce BOOLEAN DEFAULT false NOT NULL, identifier VARCHAR(32) NOT NULL, PRIMARY KEY(identifier))');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE member_season (is_secondary_club BOOLEAN DEFAULT false NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, member_id INT NOT NULL, season_id INT NOT NULL, age_category_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_d432d99ae1f4383b ON member_season (age_category_id)');
        $this->addSql('CREATE INDEX idx_d432d99a4ec001d1 ON member_season (season_id)');
        $this->addSql('CREATE INDEX idx_d432d99a7597d3fe ON member_season (member_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_d432d99ad17f50a6 ON member_season (uuid)');
        $this->addSql('ALTER TABLE member_season ADD CONSTRAINT fk_d432d99a7597d3fe FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_season ADD CONSTRAINT fk_d432d99a4ec001d1 FOREIGN KEY (season_id) REFERENCES season (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_season ADD CONSTRAINT fk_d432d99ae1f4383b FOREIGN KEY (age_category_id) REFERENCES age_category (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE club_setting_exclude_activities_od (club_setting_id INT NOT NULL, activity_id INT NOT NULL, PRIMARY KEY(club_setting_id, activity_id))');
        $this->addSql('CREATE INDEX idx_9fe426e581c06096 ON club_setting_exclude_activities_od (activity_id)');
        $this->addSql('CREATE INDEX idx_9fe426e5a7f3f39f ON club_setting_exclude_activities_od (club_setting_id)');
        $this->addSql('ALTER TABLE club_setting_exclude_activities_od ADD CONSTRAINT fk_9fe426e5a7f3f39f FOREIGN KEY (club_setting_id) REFERENCES club_setting (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE club_setting_exclude_activities_od ADD CONSTRAINT fk_9fe426e581c06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE club_setting (itac_import_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, itac_import_remaining INT DEFAULT 0 NOT NULL, itac_secondary_import_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, itac_secondary_import_remaining INT DEFAULT 0 NOT NULL, cerbere_import_remaining INT DEFAULT 0 NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, club_id INT NOT NULL, logo_id INT DEFAULT NULL, control_shooting_activity_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_923c1d1ac94ed4a ON club_setting (control_shooting_activity_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_923c1d1af98f144a ON club_setting (logo_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_923c1d1a61190a32 ON club_setting (club_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_923c1d1ad17f50a6 ON club_setting (uuid)');
        $this->addSql('ALTER TABLE club_setting ADD CONSTRAINT fk_923c1d1a61190a32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE club_setting ADD CONSTRAINT fk_923c1d1af98f144a FOREIGN KEY (logo_id) REFERENCES file (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE club_setting ADD CONSTRAINT fk_923c1d1ac94ed4a FOREIGN KEY (control_shooting_activity_id) REFERENCES activity (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE activity (name VARCHAR(255) NOT NULL, is_enabled BOOLEAN NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_ac74095a61190a32 ON activity (club_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_ac74095ad17f50a6 ON activity (uuid)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT fk_ac74095a61190a32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE oauth2_authorization_code (identifier CHAR(80) NOT NULL, expiry TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_identifier VARCHAR(128) DEFAULT NULL, scopes TEXT DEFAULT NULL, revoked BOOLEAN NOT NULL, client VARCHAR(32) NOT NULL, PRIMARY KEY(identifier))');
        $this->addSql('CREATE INDEX idx_509fef5fc7440455 ON oauth2_authorization_code (client)');
        $this->addSql('ALTER TABLE oauth2_authorization_code ADD CONSTRAINT fk_509fef5fc7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE user_security_code (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, code VARCHAR(10) NOT NULL, trigger VARCHAR(255) NOT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, user_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_468a1109a76ed395 ON user_security_code (user_id)');
        $this->addSql('ALTER TABLE user_security_code ADD CONSTRAINT fk_468a1109a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE sale_payment_mode (name VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, available BOOLEAN NOT NULL, weight INT DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_853b81eb61190a32 ON sale_payment_mode (club_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_853b81ebd17f50a6 ON sale_payment_mode (uuid)');
        $this->addSql('ALTER TABLE sale_payment_mode ADD CONSTRAINT fk_853b81eb61190a32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE oauth2_access_token (identifier CHAR(80) NOT NULL, expiry TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_identifier VARCHAR(128) DEFAULT NULL, scopes TEXT DEFAULT NULL, revoked BOOLEAN NOT NULL, client VARCHAR(32) NOT NULL, PRIMARY KEY(identifier))');
        $this->addSql('CREATE INDEX idx_454d9673c7440455 ON oauth2_access_token (client)');
        $this->addSql('ALTER TABLE oauth2_access_token ADD CONSTRAINT fk_454d9673c7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE age_category (id INT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE season (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE sale (price NUMERIC(8, 2) NOT NULL, comment VARCHAR(255) DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, seller_id INT DEFAULT NULL, payment_mode_id INT DEFAULT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_e54bc00561190a32 ON sale (club_id)');
        $this->addSql('CREATE INDEX idx_e54bc0056eac8ba0 ON sale (payment_mode_id)');
        $this->addSql('CREATE INDEX idx_e54bc0058de820d9 ON sale (seller_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_e54bc005d17f50a6 ON sale (uuid)');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT fk_e54bc0058de820d9 FOREIGN KEY (seller_id) REFERENCES member (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT fk_e54bc0056eac8ba0 FOREIGN KEY (payment_mode_id) REFERENCES sale_payment_mode (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT fk_e54bc00561190a32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE user_member (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, role VARCHAR(255) NOT NULL, user_id INT NOT NULL, member_id INT DEFAULT NULL, badger_club_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_15b6e145a43ef515 ON user_member (badger_club_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_15b6e1457597d3fe ON user_member (member_id)');
        $this->addSql('CREATE INDEX idx_15b6e145a76ed395 ON user_member (user_id)');
        $this->addSql('ALTER TABLE user_member ADD CONSTRAINT fk_15b6e145a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_member ADD CONSTRAINT fk_15b6e1457597d3fe FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_member ADD CONSTRAINT fk_15b6e145a43ef515 FOREIGN KEY (badger_club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE sale_purchased_item (item_name VARCHAR(255) DEFAULT NULL, item_category VARCHAR(255) DEFAULT NULL, item_price NUMERIC(8, 2) NOT NULL, quantity INT NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, item_id INT DEFAULT NULL, sale_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_5f0e80a64a7e4868 ON sale_purchased_item (sale_id)');
        $this->addSql('CREATE INDEX idx_5f0e80a6126f525e ON sale_purchased_item (item_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_5f0e80a6d17f50a6 ON sale_purchased_item (uuid)');
        $this->addSql('ALTER TABLE sale_purchased_item ADD CONSTRAINT fk_5f0e80a6126f525e FOREIGN KEY (item_id) REFERENCES inventory_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale_purchased_item ADD CONSTRAINT fk_5f0e80a64a7e4868 FOREIGN KEY (sale_id) REFERENCES sale (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE inventory_item (name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, purchase_price NUMERIC(8, 2) DEFAULT NULL, can_be_sold BOOLEAN DEFAULT false NOT NULL, selling_price NUMERIC(8, 2) NOT NULL, selling_quantity INT NOT NULL, quantity INT DEFAULT NULL, quantity_alert INT DEFAULT NULL, barcode VARCHAR(255) DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, category_id INT DEFAULT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_55bdea3061190a32 ON inventory_item (club_id)');
        $this->addSql('CREATE INDEX idx_55bdea3012469de2 ON inventory_item (category_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_55bdea30d17f50a6 ON inventory_item (uuid)');
        $this->addSql('ALTER TABLE inventory_item ADD CONSTRAINT fk_55bdea3012469de2 FOREIGN KEY (category_id) REFERENCES inventory_category (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inventory_item ADD CONSTRAINT fk_55bdea3061190a32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE member (email VARCHAR(180) DEFAULT NULL, licence VARCHAR(10) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, gender VARCHAR(1) NOT NULL, birthday DATE DEFAULT NULL, handisport BOOLEAN NOT NULL, postal1 VARCHAR(255) DEFAULT NULL, postal2 VARCHAR(255) DEFAULT NULL, postal3 VARCHAR(255) DEFAULT NULL, zip_code INT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, phone VARCHAR(14) DEFAULT NULL, mobile_phone VARCHAR(14) DEFAULT NULL, blacklisted BOOLEAN NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, profile_image_id INT DEFAULT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_70e4fa7861190a32 ON member (club_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_70e4fa78c4cf44dc ON member (profile_image_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_70e4fa78d17f50a6 ON member (uuid)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT fk_70e4fa78c4cf44dc FOREIGN KEY (profile_image_id) REFERENCES file (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT fk_70e4fa7861190a32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE inventory_category (name VARCHAR(255) NOT NULL, weight INT DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_b206a35061190a32 ON inventory_category (club_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_b206a350d17f50a6 ON inventory_category (uuid)');
        $this->addSql('ALTER TABLE inventory_category ADD CONSTRAINT fk_b206a35061190a32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE global_setting (id INT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE "user" (roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, account_activated BOOLEAN NOT NULL, email VARCHAR(180) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649d17f50a6 ON "user" (uuid)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON "user" (email)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE inventory_item_history (selling_price NUMERIC(8, 2) DEFAULT NULL, purchase_price NUMERIC(8, 2) DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, item_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_27487b02126f525e ON inventory_item_history (item_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_27487b02d17f50a6 ON inventory_item_history (uuid)');
        $this->addSql('ALTER TABLE inventory_item_history ADD CONSTRAINT fk_27487b02126f525e FOREIGN KEY (item_id) REFERENCES inventory_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE file (category VARCHAR(255) NOT NULL, filename VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, is_public BOOLEAN DEFAULT false NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, club_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_8c9f361061190a32 ON file (club_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8c9f3610d17f50a6 ON file (uuid)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT fk_8c9f361061190a32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE club (name VARCHAR(255) NOT NULL, is_activated BOOLEAN DEFAULT true NOT NULL, renew_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, sales_enabled BOOLEAN DEFAULT false NOT NULL, badger_token VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, contact_name VARCHAR(255) DEFAULT NULL, contact_phone VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_b8ee3872d17f50a6 ON club (uuid)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE external_presence_activity (external_presence_id INT NOT NULL, activity_id INT NOT NULL, PRIMARY KEY(external_presence_id, activity_id))');
        $this->addSql('CREATE INDEX idx_41ca982681c06096 ON external_presence_activity (activity_id)');
        $this->addSql('CREATE INDEX idx_41ca98269befb2b3 ON external_presence_activity (external_presence_id)');
        $this->addSql('ALTER TABLE external_presence_activity ADD CONSTRAINT fk_41ca98269befb2b3 FOREIGN KEY (external_presence_id) REFERENCES external_presence (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE external_presence_activity ADD CONSTRAINT fk_41ca982681c06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE member_presence (date DATE NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, member_id INT DEFAULT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_9eaa644f61190a32 ON member_presence (club_id)');
        $this->addSql('CREATE INDEX idx_9eaa644f7597d3fe ON member_presence (member_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_9eaa644fd17f50a6 ON member_presence (uuid)');
        $this->addSql('ALTER TABLE member_presence ADD CONSTRAINT fk_9eaa644f7597d3fe FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_presence ADD CONSTRAINT fk_9eaa644f61190a32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE messenger_messages (id BIGINT GENERATED BY DEFAULT AS IDENTITY NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_75ea56e016ba31db ON messenger_messages (delivered_at)');
        $this->addSql('CREATE INDEX idx_75ea56e0e3bd61ce ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX idx_75ea56e0fb7336f0 ON messenger_messages (queue_name)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE oauth2_refresh_token (identifier CHAR(80) NOT NULL, expiry TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, revoked BOOLEAN NOT NULL, access_token CHAR(80) DEFAULT NULL, PRIMARY KEY(identifier))');
        $this->addSql('CREATE INDEX idx_4dd90732b6a2dd68 ON oauth2_refresh_token (access_token)');
        $this->addSql('ALTER TABLE oauth2_refresh_token ADD CONSTRAINT fk_4dd90732b6a2dd68 FOREIGN KEY (access_token) REFERENCES oauth2_access_token (identifier) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE member_presence_activity (member_presence_id INT NOT NULL, activity_id INT NOT NULL, PRIMARY KEY(member_presence_id, activity_id))');
        $this->addSql('CREATE INDEX idx_1389d5be81c06096 ON member_presence_activity (activity_id)');
        $this->addSql('CREATE INDEX idx_1389d5be2a15eb06 ON member_presence_activity (member_presence_id)');
        $this->addSql('ALTER TABLE member_presence_activity ADD CONSTRAINT fk_1389d5be2a15eb06 FOREIGN KEY (member_presence_id) REFERENCES member_presence (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_presence_activity ADD CONSTRAINT fk_1389d5be81c06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('CREATE TABLE external_presence (licence VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, date DATE NOT NULL, id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, club_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_867b755661190a32 ON external_presence (club_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_867b7556d17f50a6 ON external_presence (uuid)');
        $this->addSql('ALTER TABLE external_presence ADD CONSTRAINT fk_867b755661190a32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE oauth2_client');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE member_season');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE club_setting_exclude_activities_od');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE club_setting');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE activity');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE oauth2_authorization_code');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE user_security_code');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE sale_payment_mode');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE oauth2_access_token');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE age_category');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE season');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE sale');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE user_member');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE sale_purchased_item');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE inventory_item');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE member');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE inventory_category');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE global_setting');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE "user"');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE inventory_item_history');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE file');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE club');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE external_presence_activity');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE member_presence');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE messenger_messages');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE oauth2_refresh_token');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE member_presence_activity');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
        );

        $this->addSql('DROP TABLE external_presence');
    }
}
