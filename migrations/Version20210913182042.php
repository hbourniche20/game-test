<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210913182042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE char_enemy (id INT AUTO_INCREMENT NOT NULL, char_classe_id INT DEFAULT NULL, base_attribute JSON NOT NULL, INDEX IDX_9932820841067D31 (char_classe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE raid_quest (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, data_conf JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE char_enemy ADD CONSTRAINT FK_9932820841067D31 FOREIGN KEY (char_classe_id) REFERENCES char_classe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE char_enemy');
        $this->addSql('DROP TABLE raid_quest');
    }
}
