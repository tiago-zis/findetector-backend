<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223190356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE management.terms_of_use_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE management.user_terms_of_use_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE management.terms_of_use (id INT NOT NULL, createdby INT DEFAULT NULL, updatedby INT DEFAULT NULL, deletedby INT DEFAULT NULL, validatedby INT DEFAULT NULL, content TEXT NOT NULL, version VARCHAR(255) NOT NULL, createdat TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deletedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, validatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EDE0F00E46D262E0 ON management.terms_of_use (createdby)');
        $this->addSql('CREATE INDEX IDX_EDE0F00E7D5A55D2 ON management.terms_of_use (updatedby)');
        $this->addSql('CREATE INDEX IDX_EDE0F00E5AC430DE ON management.terms_of_use (deletedby)');
        $this->addSql('CREATE INDEX IDX_EDE0F00EBBF1D944 ON management.terms_of_use (validatedby)');
        $this->addSql('CREATE TABLE management.user_terms_of_use (id INT NOT NULL, user_id INT NOT NULL, accepted BOOLEAN DEFAULT NULL, acceptance_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6D5C6BDFA76ED395 ON management.user_terms_of_use (user_id)');
        $this->addSql('ALTER TABLE management.terms_of_use ADD CONSTRAINT FK_EDE0F00E46D262E0 FOREIGN KEY (createdby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE management.terms_of_use ADD CONSTRAINT FK_EDE0F00E7D5A55D2 FOREIGN KEY (updatedby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE management.terms_of_use ADD CONSTRAINT FK_EDE0F00E5AC430DE FOREIGN KEY (deletedby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE management.terms_of_use ADD CONSTRAINT FK_EDE0F00EBBF1D944 FOREIGN KEY (validatedby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE management.user_terms_of_use ADD CONSTRAINT FK_6D5C6BDFA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA tmp_geodata');
        $this->addSql('DROP SEQUENCE management.terms_of_use_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE management.user_terms_of_use_id_seq CASCADE');
        $this->addSql('ALTER TABLE management.terms_of_use DROP CONSTRAINT FK_EDE0F00E46D262E0');
        $this->addSql('ALTER TABLE management.terms_of_use DROP CONSTRAINT FK_EDE0F00E7D5A55D2');
        $this->addSql('ALTER TABLE management.terms_of_use DROP CONSTRAINT FK_EDE0F00E5AC430DE');
        $this->addSql('ALTER TABLE management.terms_of_use DROP CONSTRAINT FK_EDE0F00EBBF1D944');
        $this->addSql('ALTER TABLE management.user_terms_of_use DROP CONSTRAINT FK_6D5C6BDFA76ED395');
        $this->addSql('DROP TABLE management.terms_of_use');
        $this->addSql('DROP TABLE management.user_terms_of_use');
    }
}
