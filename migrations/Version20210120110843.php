<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210120110843 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(<<<SQL
            CREATE TABLE path_map (
                id INT AUTO_INCREMENT NOT NULL,
                path_id INT NOT NULL,
                url VARCHAR(255) NOT NULL,
                priority INT NOT NULL DEFAULT 0,
                INDEX IDX_22C016AED96C566B (path_id),
                PRIMARY KEY(id)
           )
           DEFAULT CHARACTER SET utf8mb4
           COLLATE `utf8mb4_unicode_ci`
           ENGINE = InnoDB
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE path_map ADD CONSTRAINT FK_22C016AED96C566B FOREIGN KEY (path_id) REFERENCES path (id)
        SQL);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE path_map');
    }
}
