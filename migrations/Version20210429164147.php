<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210429164147 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE SEQUENCE path_media_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE media_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE media (id INT NOT NULL, content BYTEA NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE path_media (id INT NOT NULL, path_id INT NOT NULL UNIQUE, media_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9F4E09D2D96C566B ON path_media (path_id)');
        $this->addSql('ALTER TABLE path_media ADD CONSTRAINT FK_9F4E09D2D96C566B FOREIGN KEY (path_id) REFERENCES path (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE path_media ADD CONSTRAINT FK_9F4E09D2D96C566C FOREIGN KEY (media_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CC54C8C93');
        $this->addSql('DROP SEQUENCE media_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE path_media_id_seq CASCADE');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE path_media');
    }
}
