<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513140751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_group DROP FOREIGN KEY FK_DB486FE1A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_group DROP FOREIGN KEY FK_DB486FE1FE54D947
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_group ADD CONSTRAINT FK_DB486FE1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_group ADD CONSTRAINT FK_DB486FE1FE54D947 FOREIGN KEY (group_id) REFERENCES work_group (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post ADD is_draft TINYINT(1) NOT NULL, ADD tags JSON NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE post DROP is_draft, DROP tags
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_group DROP FOREIGN KEY FK_DB486FE1A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_group DROP FOREIGN KEY FK_DB486FE1FE54D947
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_group ADD CONSTRAINT FK_DB486FE1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_group ADD CONSTRAINT FK_DB486FE1FE54D947 FOREIGN KEY (group_id) REFERENCES work_group (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
    }
}
