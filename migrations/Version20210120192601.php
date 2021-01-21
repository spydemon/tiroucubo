<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210120192601 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(<<<SQL
            CREATE TABLE path_translation (
                id INT AUTO_INCREMENT NOT NULL,
                path_fr VARCHAR(255) DEFAULT NULL UNIQUE,
                path_en VARCHAR(255) DEFAULT NULL UNIQUE,
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4 
            COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE path_translation');
    }
}
