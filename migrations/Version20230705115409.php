<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230705115409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sponsors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, commercial_zone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsors_team (sponsors_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_49415372FB0F2BBC (sponsors_id), INDEX IDX_49415372296CD8AE (team_id), PRIMARY KEY(sponsors_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sponsors_team ADD CONSTRAINT FK_49415372FB0F2BBC FOREIGN KEY (sponsors_id) REFERENCES sponsors (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sponsors_team ADD CONSTRAINT FK_49415372296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sponsors_team DROP FOREIGN KEY FK_49415372FB0F2BBC');
        $this->addSql('ALTER TABLE sponsors_team DROP FOREIGN KEY FK_49415372296CD8AE');
        $this->addSql('DROP TABLE sponsors');
        $this->addSql('DROP TABLE sponsors_team');
    }
}
