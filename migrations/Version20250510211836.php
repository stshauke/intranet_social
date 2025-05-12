<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250510211836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD related_message_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAD9B71B99 FOREIGN KEY (related_message_id) REFERENCES message (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF5476CAD9B71B99 ON notification (related_message_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX user_type_unique ON notification_preference (user_id, type)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX user_type_unique ON notification_preference
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAD9B71B99
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BF5476CAD9B71B99 ON notification
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP related_message_id
        SQL);
    }
}
