<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221104182934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA management');
        $this->addSql('CREATE SCHEMA data');
        $this->addSql('CREATE SEQUENCE management.drive_meta_data_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE data.file_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE management.drive_meta_data (id INT NOT NULL, drive_id VARCHAR(255) NOT NULL, folder_name VARCHAR(255) NOT NULL, data_type VARCHAR(255) NOT NULL, meta_data JSON DEFAULT NULL, parent_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE data.file (id INT NOT NULL, createdby INT DEFAULT NULL, updatedby INT DEFAULT NULL, deletedby INT DEFAULT NULL, validatedby INT DEFAULT NULL, name VARCHAR(255) NOT NULL, mime VARCHAR(255) NOT NULL, size INT NOT NULL, drive_id VARCHAR(255) NOT NULL, createdat TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deletedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, validatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F362348D46D262E0 ON data.file (createdby)');
        $this->addSql('CREATE INDEX IDX_F362348D7D5A55D2 ON data.file (updatedby)');
        $this->addSql('CREATE INDEX IDX_F362348D5AC430DE ON data.file (deletedby)');
        $this->addSql('CREATE INDEX IDX_F362348DBBF1D944 ON data.file (validatedby)');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE data.file ADD CONSTRAINT FK_F362348D46D262E0 FOREIGN KEY (createdby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.file ADD CONSTRAINT FK_F362348D7D5A55D2 FOREIGN KEY (updatedby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.file ADD CONSTRAINT FK_F362348D5AC430DE FOREIGN KEY (deletedby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.file ADD CONSTRAINT FK_F362348DBBF1D944 FOREIGN KEY (validatedby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE management.drive_meta_data_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE data.file_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE data.file DROP CONSTRAINT FK_F362348D46D262E0');
        $this->addSql('ALTER TABLE data.file DROP CONSTRAINT FK_F362348D7D5A55D2');
        $this->addSql('ALTER TABLE data.file DROP CONSTRAINT FK_F362348D5AC430DE');
        $this->addSql('ALTER TABLE data.file DROP CONSTRAINT FK_F362348DBBF1D944');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE management.drive_meta_data');
        $this->addSql('DROP TABLE data.file');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE "user"');
    }
}
