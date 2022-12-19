<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221124165431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE data.image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE data.image (id INT NOT NULL, file_id INT NOT NULL, createdby INT DEFAULT NULL, updatedby INT DEFAULT NULL, deletedby INT DEFAULT NULL, validatedby INT DEFAULT NULL, status VARCHAR(20) NOT NULL, processed_data JSON DEFAULT NULL, processing_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, createdat TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deletedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, validatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4BFC16A493CB796C ON data.image (file_id)');
        $this->addSql('CREATE INDEX IDX_4BFC16A446D262E0 ON data.image (createdby)');
        $this->addSql('CREATE INDEX IDX_4BFC16A47D5A55D2 ON data.image (updatedby)');
        $this->addSql('CREATE INDEX IDX_4BFC16A45AC430DE ON data.image (deletedby)');
        $this->addSql('CREATE INDEX IDX_4BFC16A4BBF1D944 ON data.image (validatedby)');
        $this->addSql('ALTER TABLE data.image ADD CONSTRAINT FK_4BFC16A493CB796C FOREIGN KEY (file_id) REFERENCES data.file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.image ADD CONSTRAINT FK_4BFC16A446D262E0 FOREIGN KEY (createdby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.image ADD CONSTRAINT FK_4BFC16A47D5A55D2 FOREIGN KEY (updatedby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.image ADD CONSTRAINT FK_4BFC16A45AC430DE FOREIGN KEY (deletedby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.image ADD CONSTRAINT FK_4BFC16A4BBF1D944 FOREIGN KEY (validatedby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.file ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE data.file ALTER mime DROP NOT NULL');
        $this->addSql('ALTER TABLE data.file ALTER size DROP NOT NULL');
        $this->addSql('ALTER TABLE data.file ALTER drive_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE data.image_id_seq CASCADE');
        $this->addSql('ALTER TABLE data.image DROP CONSTRAINT FK_4BFC16A493CB796C');
        $this->addSql('ALTER TABLE data.image DROP CONSTRAINT FK_4BFC16A446D262E0');
        $this->addSql('ALTER TABLE data.image DROP CONSTRAINT FK_4BFC16A47D5A55D2');
        $this->addSql('ALTER TABLE data.image DROP CONSTRAINT FK_4BFC16A45AC430DE');
        $this->addSql('ALTER TABLE data.image DROP CONSTRAINT FK_4BFC16A4BBF1D944');
        $this->addSql('DROP TABLE data.image');
        $this->addSql('ALTER TABLE data.file DROP original_name');
        $this->addSql('ALTER TABLE data.file ALTER mime SET NOT NULL');
        $this->addSql('ALTER TABLE data.file ALTER size SET NOT NULL');
        $this->addSql('ALTER TABLE data.file ALTER drive_id SET NOT NULL');
    }
}
