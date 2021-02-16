<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210215151259 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(<<<SQL
            CREATE TABLE article_version (
                id INT AUTO_INCREMENT NOT NULL,
                article_id INT NOT NULL,
                slug VARCHAR(8) NOT NULL,
                creation_date DATETIME NOT NULL,
                commit_message VARCHAR(255) NOT NULL,
                active TINYINT(1) NOT NULL,
                summary LONGTEXT DEFAULT NULL,
                content LONGTEXT NOT NULL, INDEX IDX_52CE97747294869C (article_id), PRIMARY KEY(id)
           ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql('ALTER TABLE article_version ADD CONSTRAINT FK_52CE97747294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE article_version ADD CONSTRAINT UNIQ_article_id_slug UNIQUE(article_id, slug)');

        /**
         * The followed procedure ensure that only a single version of a each article is active. The check is run at
         * each insert and update.
         */
        $this->addSql(<<<SQL
            CREATE PROCEDURE article_version_check_active_integrity(in article_id int)
            BEGIN
                DECLARE result int;
                SELECT COUNT(*) INTO result FROM article_version AS t WHERE article_id = t.article_id AND active = true;
                IF (result > 1) THEN
                    SIGNAL SQLSTATE '45000'
                           SET MESSAGE_TEXT = 'Query aborted since it activates more than one version for a single article.';
                END IF;
            END;
        SQL);
        $this->addSql(<<<SQL
            CREATE TRIGGER article_version_after_insert
                AFTER INSERT ON article_version 
                FOR EACH ROW
            BEGIN
                CALL article_version_check_active_integrity(NEW.article_id);
            END
        SQL);
        $this->addSql(<<<SQL
            CREATE TRIGGER article_version_after_update
                AFTER UPDATE ON article_version 
                FOR EACH ROW
            BEGIN
                CALL article_version_check_active_integrity(NEW.article_id);
            END
        SQL);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE article_version');
        $this->addSql('DROP PROCEDURE article_version_check_active_integrity');
    }
}
