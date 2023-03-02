<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230227143956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE management.user_terms_of_use ADD terms_id INT NOT NULL');
        $this->addSql('ALTER TABLE management.user_terms_of_use ADD CONSTRAINT FK_6D5C6BDF53742F27 FOREIGN KEY (terms_id) REFERENCES management.terms_of_use (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6D5C6BDF53742F27 ON management.user_terms_of_use (terms_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA tmp_geodata');
        $this->addSql('ALTER TABLE management.user_terms_of_use DROP CONSTRAINT FK_6D5C6BDF53742F27');
        $this->addSql('DROP INDEX IDX_6D5C6BDF53742F27');
        $this->addSql('ALTER TABLE management.user_terms_of_use DROP terms_id');
    }
}
