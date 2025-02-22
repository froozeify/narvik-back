<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222101719 extends AbstractMigration {
  public function getDescription(): string {
    return 'Add medical certificate expiration date on member';
  }

  public function up(Schema $schema): void {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE member ADD medical_certificate_expiration TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
  }

  public function down(Schema $schema): void {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE member DROP medical_certificate_expiration');
  }
}
