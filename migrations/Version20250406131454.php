<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250406131454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE like_comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, comment_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_C7F9184FA76ED395 (user_id), INDEX IDX_C7F9184FF8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE like_comment ADD CONSTRAINT FK_C7F9184FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE like_comment ADD CONSTRAINT FK_C7F9184FF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE like_comment DROP FOREIGN KEY FK_C7F9184FA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE like_comment DROP FOREIGN KEY FK_C7F9184FF8697D13
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE like_comment
        SQL);
    }
}
