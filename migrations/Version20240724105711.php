<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240724105711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE inventory_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE inventory_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE inventory_item_history_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sale_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sale_payment_mode_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sale_purchased_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE inventory_category (id INT NOT NULL, name VARCHAR(255) NOT NULL, weight INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE inventory_item (id INT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, purchase_price NUMERIC(8, 2) DEFAULT NULL, can_be_sold BOOLEAN NOT NULL, selling_price NUMERIC(8, 2) NOT NULL, selling_quantity INT NOT NULL, quantity INT DEFAULT NULL, barcode VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_55BDEA3012469DE2 ON inventory_item (category_id)');
        $this->addSql('COMMENT ON COLUMN inventory_item.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN inventory_item.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE inventory_item_history (id INT NOT NULL, item_id INT NOT NULL, selling_price NUMERIC(8, 2) DEFAULT NULL, purchase_price NUMERIC(8, 2) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_27487B02126F525E ON inventory_item_history (item_id)');
        $this->addSql('COMMENT ON COLUMN inventory_item_history.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN inventory_item_history.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE sale (id INT NOT NULL, seller_id INT DEFAULT NULL, payment_mode_id INT DEFAULT NULL, price NUMERIC(8, 2) NOT NULL, comment VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E54BC0058DE820D9 ON sale (seller_id)');
        $this->addSql('CREATE INDEX IDX_E54BC0056EAC8BA0 ON sale (payment_mode_id)');
        $this->addSql('COMMENT ON COLUMN sale.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN sale.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE sale_payment_mode (id INT NOT NULL, name VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, available BOOLEAN NOT NULL, weight INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE sale_purchased_item (id INT NOT NULL, item_id INT DEFAULT NULL, sale_id INT DEFAULT NULL, item_name VARCHAR(255) DEFAULT NULL, item_category VARCHAR(255) DEFAULT NULL, item_price NUMERIC(8, 2) NOT NULL, quantity INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5F0E80A6126F525E ON sale_purchased_item (item_id)');
        $this->addSql('CREATE INDEX IDX_5F0E80A64A7E4868 ON sale_purchased_item (sale_id)');
        $this->addSql('COMMENT ON COLUMN sale_purchased_item.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN sale_purchased_item.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE inventory_item ADD CONSTRAINT FK_55BDEA3012469DE2 FOREIGN KEY (category_id) REFERENCES inventory_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inventory_item_history ADD CONSTRAINT FK_27487B02126F525E FOREIGN KEY (item_id) REFERENCES inventory_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT FK_E54BC0058DE820D9 FOREIGN KEY (seller_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT FK_E54BC0056EAC8BA0 FOREIGN KEY (payment_mode_id) REFERENCES sale_payment_mode (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale_purchased_item ADD CONSTRAINT FK_5F0E80A6126F525E FOREIGN KEY (item_id) REFERENCES inventory_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale_purchased_item ADD CONSTRAINT FK_5F0E80A64A7E4868 FOREIGN KEY (sale_id) REFERENCES sale (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE external_presence ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE external_presence ALTER created_at DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN external_presence.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE member_presence ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE member_presence ALTER created_at DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN member_presence.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE inventory_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE inventory_item_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE inventory_item_history_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sale_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sale_payment_mode_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sale_purchased_item_id_seq CASCADE');
        $this->addSql('ALTER TABLE inventory_item DROP CONSTRAINT FK_55BDEA3012469DE2');
        $this->addSql('ALTER TABLE inventory_item_history DROP CONSTRAINT FK_27487B02126F525E');
        $this->addSql('ALTER TABLE sale DROP CONSTRAINT FK_E54BC0058DE820D9');
        $this->addSql('ALTER TABLE sale DROP CONSTRAINT FK_E54BC0056EAC8BA0');
        $this->addSql('ALTER TABLE sale_purchased_item DROP CONSTRAINT FK_5F0E80A6126F525E');
        $this->addSql('ALTER TABLE sale_purchased_item DROP CONSTRAINT FK_5F0E80A64A7E4868');
        $this->addSql('DROP TABLE inventory_category');
        $this->addSql('DROP TABLE inventory_item');
        $this->addSql('DROP TABLE inventory_item_history');
        $this->addSql('DROP TABLE sale');
        $this->addSql('DROP TABLE sale_payment_mode');
        $this->addSql('DROP TABLE sale_purchased_item');
        $this->addSql('ALTER TABLE external_presence DROP updated_at');
        $this->addSql('ALTER TABLE external_presence ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE member_presence DROP updated_at');
        $this->addSql('ALTER TABLE member_presence ALTER created_at SET NOT NULL');
    }
}
