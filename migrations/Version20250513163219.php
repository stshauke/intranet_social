<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513163219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql(<<<'SQL'
        //    ALTER TABLE user ADD username VARCHAR(50) NOT NULL
        //SQL);
        //$this->addSql(<<<'SQL'
          //  CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)
        //SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8D93D649F85E0677 ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP username
        SQL);
    }
}
