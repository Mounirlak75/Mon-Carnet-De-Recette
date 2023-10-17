<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231004144210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recette CHANGE temps_de_preparation temps_de_preparation VARCHAR(255) NOT NULL, CHANGE temps_de_cuisson temps_de_cuisson VARCHAR(255) NOT NULL, CHANGE difficulte difficulte VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recette CHANGE temps_de_preparation temps_de_preparation INT NOT NULL, CHANGE temps_de_cuisson temps_de_cuisson INT NOT NULL, CHANGE difficulte difficulte INT NOT NULL');
    }
}
