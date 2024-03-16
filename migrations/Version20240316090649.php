<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240316090649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
      $this->addSql('CREATE EXTENSION unaccent;');
    }

    public function down(Schema $schema): void
    {
      $this->addSql('DROP EXTENSION unaccent;');
    }
}
