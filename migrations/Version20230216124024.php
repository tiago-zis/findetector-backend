<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216124024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE management.detection_process_error_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE management.detection_process_error (id INT NOT NULL, image_id INT NOT NULL, createdby INT DEFAULT NULL, updatedby INT DEFAULT NULL, deletedby INT DEFAULT NULL, validatedby INT DEFAULT NULL, message TEXT DEFAULT NULL, error JSON DEFAULT NULL, createdat TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deletedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, validatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7163BFB83DA5256D ON management.detection_process_error (image_id)');
        $this->addSql('CREATE INDEX IDX_7163BFB846D262E0 ON management.detection_process_error (createdby)');
        $this->addSql('CREATE INDEX IDX_7163BFB87D5A55D2 ON management.detection_process_error (updatedby)');
        $this->addSql('CREATE INDEX IDX_7163BFB85AC430DE ON management.detection_process_error (deletedby)');
        $this->addSql('CREATE INDEX IDX_7163BFB8BBF1D944 ON management.detection_process_error (validatedby)');
        $this->addSql('ALTER TABLE management.detection_process_error ADD CONSTRAINT FK_7163BFB83DA5256D FOREIGN KEY (image_id) REFERENCES data.image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE management.detection_process_error ADD CONSTRAINT FK_7163BFB846D262E0 FOREIGN KEY (createdby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE management.detection_process_error ADD CONSTRAINT FK_7163BFB87D5A55D2 FOREIGN KEY (updatedby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE management.detection_process_error ADD CONSTRAINT FK_7163BFB85AC430DE FOREIGN KEY (deletedby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE management.detection_process_error ADD CONSTRAINT FK_7163BFB8BBF1D944 FOREIGN KEY (validatedby) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA tmp_geodata');
        $this->addSql('DROP SEQUENCE management.detection_process_error_id_seq CASCADE');
        $this->addSql('ALTER TABLE management.detection_process_error DROP CONSTRAINT FK_7163BFB83DA5256D');
        $this->addSql('ALTER TABLE management.detection_process_error DROP CONSTRAINT FK_7163BFB846D262E0');
        $this->addSql('ALTER TABLE management.detection_process_error DROP CONSTRAINT FK_7163BFB87D5A55D2');
        $this->addSql('ALTER TABLE management.detection_process_error DROP CONSTRAINT FK_7163BFB85AC430DE');
        $this->addSql('ALTER TABLE management.detection_process_error DROP CONSTRAINT FK_7163BFB8BBF1D944');
        $this->addSql('DROP TABLE management.detection_process_error');
    }
}
