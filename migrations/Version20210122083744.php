<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210122083744 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(<<<SQL
            CREATE TABLE article (
                id INT AUTO_INCREMENT NOT NULL,
                path_id INT DEFAULT NULL,
                title VARCHAR(255) NOT NULL,
                creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                update_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                summary LONGTEXT DEFAULT NULL,
                content LONGTEXT NOT NULL,
                INDEX IDX_23A0E66D96C566B (path_id),
                PRIMARY KEY(id)
           )
           DEFAULT CHARACTER SET utf8mb4
           COLLATE `utf8mb4_unicode_ci`
           ENGINE = InnoDB
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE article ADD CONSTRAINT FK_23A0E66D96C566B FOREIGN KEY (path_id) REFERENCES path (id)
       SQL);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE article');
    }
}
