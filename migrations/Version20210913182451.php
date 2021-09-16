<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210913182451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE raid_game ADD raid_quest_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE raid_game ADD CONSTRAINT FK_117AAD287DE96CA7 FOREIGN KEY (raid_quest_id) REFERENCES raid_quest (id)');
        $this->addSql('CREATE INDEX IDX_117AAD287DE96CA7 ON raid_game (raid_quest_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE raid_game DROP FOREIGN KEY FK_117AAD287DE96CA7');
        $this->addSql('DROP INDEX IDX_117AAD287DE96CA7 ON raid_game');
        $this->addSql('ALTER TABLE raid_game DROP raid_quest_id');
    }
}
