<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210912230852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ability (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, move_anim_data JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE char_class (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, char_image_lib_list JSON DEFAULT NULL, char_lib_list JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE char_classe (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE char_classe_ability (char_classe_id INT NOT NULL, ability_id INT NOT NULL, INDEX IDX_B3169A441067D31 (char_classe_id), INDEX IDX_B3169A48016D8B2 (ability_id), PRIMARY KEY(char_classe_id, ability_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE char_player (id INT AUTO_INCREMENT NOT NULL, classe_id INT DEFAULT NULL, user_id INT DEFAULT NULL, level INT NOT NULL, INDEX IDX_39AA44C38F5EA509 (classe_id), INDEX IDX_39AA44C3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE raid_character (id INT AUTO_INCREMENT NOT NULL, raid_team_id INT DEFAULT NULL, char_player_id INT DEFAULT NULL, current_attr_data JSON NOT NULL, base_attr_data JSON NOT NULL, INDEX IDX_6145C78836858954 (raid_team_id), INDEX IDX_6145C78857BE2DE7 (char_player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE raid_game (id INT AUTO_INCREMENT NOT NULL, background_url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE raid_team (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE char_classe_ability ADD CONSTRAINT FK_B3169A441067D31 FOREIGN KEY (char_classe_id) REFERENCES char_classe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE char_classe_ability ADD CONSTRAINT FK_B3169A48016D8B2 FOREIGN KEY (ability_id) REFERENCES ability (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE char_player ADD CONSTRAINT FK_39AA44C38F5EA509 FOREIGN KEY (classe_id) REFERENCES char_classe (id)');
        $this->addSql('ALTER TABLE char_player ADD CONSTRAINT FK_39AA44C3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE raid_character ADD CONSTRAINT FK_6145C78836858954 FOREIGN KEY (raid_team_id) REFERENCES raid_team (id)');
        $this->addSql('ALTER TABLE raid_character ADD CONSTRAINT FK_6145C78857BE2DE7 FOREIGN KEY (char_player_id) REFERENCES char_player (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE char_classe_ability DROP FOREIGN KEY FK_B3169A48016D8B2');
        $this->addSql('ALTER TABLE char_classe_ability DROP FOREIGN KEY FK_B3169A441067D31');
        $this->addSql('ALTER TABLE char_player DROP FOREIGN KEY FK_39AA44C38F5EA509');
        $this->addSql('ALTER TABLE raid_character DROP FOREIGN KEY FK_6145C78857BE2DE7');
        $this->addSql('ALTER TABLE raid_character DROP FOREIGN KEY FK_6145C78836858954');
        $this->addSql('ALTER TABLE char_player DROP FOREIGN KEY FK_39AA44C3A76ED395');
        $this->addSql('DROP TABLE ability');
        $this->addSql('DROP TABLE char_class');
        $this->addSql('DROP TABLE char_classe');
        $this->addSql('DROP TABLE char_classe_ability');
        $this->addSql('DROP TABLE char_player');
        $this->addSql('DROP TABLE raid_character');
        $this->addSql('DROP TABLE raid_game');
        $this->addSql('DROP TABLE raid_team');
        $this->addSql('DROP TABLE user');
    }
}
