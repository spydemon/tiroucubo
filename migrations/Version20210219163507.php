<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @package DoctrineMigrations
 */
final class Version20210219163507 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        /**
         * Article table creation.
         */
        $this->addSql('CREATE SEQUENCE article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(<<<SQL
            CREATE TABLE article (
                id INT NOT NULL,
                path_id INT DEFAULT NULL,
                title VARCHAR(255) NOT NULL,
                creation_date TIMESTAMP(0) WITH TIME ZONE NOT NULL,
                update_date TIMESTAMP(0) WITH TIME ZONE NOT NULL,
                PRIMARY KEY(id)
           )
        SQL);
        $this->addSql('CREATE INDEX IDX_23A0E66D96C566B ON article (path_id)');

        /**
         * Article_version table creation.
         */
        $this->addSql('CREATE SEQUENCE article_version_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(<<<SQL
            CREATE TABLE article_version (
                id INT NOT NULL,
                article_id INT NOT NULL,
                slug VARCHAR(8) NOT NULL,
                creation_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                commit_message VARCHAR(255) NOT NULL,
                active BOOLEAN NOT NULL,
                summary TEXT DEFAULT NULL,
                content TEXT NOT NULL,
                PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_52CE97747294869C ON article_version (article_id)');
        $this->addSql(<<<SQL
            CREATE OR REPLACE FUNCTION article_version_check_active_integrity()
            RETURNS trigger AS
            $$
                DECLARE result INT;
                BEGIN
                    result := (
                        SELECT COUNT(*) FROM article_version AS t WHERE new.article_id = t.article_id AND active = true
                    );
                    IF (result > 1) THEN
                        raise 'Query aborted since it activates more than one version for the article with ID: %.', new.article_id;
                    END IF;
                    RETURN null;
                END;
            $$
            LANGUAGE 'plpgsql';
        SQL);
        $this->addSQL(<<<SQL
            CREATE TRIGGER article_version_after_insert
                AFTER INSERT ON article_version
                FOR EACH ROW
            EXECUTE PROCEDURE article_version_check_active_integrity();
        SQL);
        $this->addSQL(<<<SQL
            CREATE TRIGGER article_version_after_update
                AFTER UPDATE ON article_version
                FOR EACH ROW
            EXECUTE PROCEDURE article_version_check_active_integrity();
        SQL);

        /**
         * Path table creation.
         */
        $this->addSql('CREATE SEQUENCE path_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(<<<SQL
            CREATE TABLE path (
                id INT NOT NULL,
                parent_id INT DEFAULT NULL,
                slug VARCHAR(255) NOT NULL,
                title VARCHAR(255) NOT NULL,
                type INT NOT NULL,
                PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_B548B0F727ACA70 ON path (parent_id)');

        /**
         * Path_map table creation.
         */
        $this->addSql('CREATE SEQUENCE path_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(<<<SQL
            CREATE TABLE path_map (
                id INT NOT NULL,
                path_id INT NOT NULL,
                url VARCHAR(255) NOT NULL,
                priority INT NOT NULL,
                PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_22C016AED96C566B ON path_map (path_id)');

        /**
         * Path_translation table creation.
         */
        $this->addSql('CREATE SEQUENCE path_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(<<<SQL
            CREATE TABLE path_translation (
                id INT NOT NULL,
                path_fr VARCHAR(255) DEFAULT NULL,
                path_en VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY(id)
            )
        SQL);

        /**
         * User_table table creation
         */
        $this->addSql('CREATE SEQUENCE user_table_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(<<<SQL
            CREATE TABLE user_table (
                id INT NOT NULL,
                email VARCHAR(255) NOT NULL,
                roles JSON NOT NULL,
                password VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_14EB741EE7927C74 ON user_table (email)');

        /**
         * Set relation between tables.
         */
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66D96C566B FOREIGN KEY (path_id) REFERENCES path (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_version ADD CONSTRAINT FK_52CE97747294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE path ADD CONSTRAINT FK_B548B0F727ACA70 FOREIGN KEY (parent_id) REFERENCES path (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE path_map ADD CONSTRAINT FK_22C016AED96C566B FOREIGN KEY (path_id) REFERENCES path (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE article_version DROP CONSTRAINT FK_52CE97747294869C');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E66D96C566B');
        $this->addSql('ALTER TABLE path DROP CONSTRAINT FK_B548B0F727ACA70');
        $this->addSql('ALTER TABLE path_map DROP CONSTRAINT FK_22C016AED96C566B');
        $this->addSql('DROP SEQUENCE article_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE article_version_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE path_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE path_map_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE path_translation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_table_id_seq CASCADE');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE article_version');
        $this->addSql('DROP TABLE path');
        $this->addSql('DROP TABLE path_map');
        $this->addSql('DROP TABLE path_translation');
        $this->addSql('DROP TABLE user_table');
    }
}
