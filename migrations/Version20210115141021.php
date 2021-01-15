<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210115141021 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(<<<SQL
            CREATE TABLE path (
                id INT AUTO_INCREMENT NOT NULL,
                parent_id INT DEFAULT NULL,
                slug VARCHAR(255) NOT NULL,
                title VARCHAR(255) NOT NULL,
                INDEX IDX_64C19C1727ACA70 (parent_id),
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE path
                ADD CONSTRAINT FK_64C19C1727ACA70
                    FOREIGN KEY (parent_id)
                    REFERENCES path (id)
                    ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE path DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('DROP TABLE path');
    }
}
