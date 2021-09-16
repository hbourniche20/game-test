<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210913183327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE raid_team ADD raid_game_id INT DEFAULT NULL, ADD kind VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE raid_team ADD CONSTRAINT FK_F6B13ABBFB6688FF FOREIGN KEY (raid_game_id) REFERENCES raid_game (id)');
        $this->addSql('CREATE INDEX IDX_F6B13ABBFB6688FF ON raid_team (raid_game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE raid_team DROP FOREIGN KEY FK_F6B13ABBFB6688FF');
        $this->addSql('DROP INDEX IDX_F6B13ABBFB6688FF ON raid_team');
        $this->addSql('ALTER TABLE raid_team DROP raid_game_id, DROP kind');
    }
}
