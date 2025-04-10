<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250409145632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE group_message (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, work_group_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_30BD6473F675F31B (author_id), INDEX IDX_30BD64732BE1531B (work_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE group_message ADD CONSTRAINT FK_30BD6473F675F31B FOREIGN KEY (author_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE group_message ADD CONSTRAINT FK_30BD64732BE1531B FOREIGN KEY (work_group_id) REFERENCES work_group (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE attachment DROP created_at, CHANGE mime_type mime_type VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post CHANGE updated_at updated_at DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE group_message DROP FOREIGN KEY FK_30BD6473F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE group_message DROP FOREIGN KEY FK_30BD64732BE1531B
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE group_message
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE attachment ADD created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE mime_type mime_type VARCHAR(100) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group CHANGE updated_at updated_at DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post CHANGE updated_at updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }
}
