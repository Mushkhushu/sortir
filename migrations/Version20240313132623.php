<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240313132623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lieu ADD nom VARCHAR(255) NOT NULL, ADD rue VARCHAR(255) DEFAULT NULL, DROP code_postal');
        $this->addSql('ALTER TABLE users CHANGE site_de_rattachement site_de_rattachement VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lieu ADD code_postal INT NOT NULL, DROP nom, DROP rue');
        $this->addSql('ALTER TABLE `users` CHANGE site_de_rattachement site_de_rattachement VARCHAR(255) DEFAULT NULL');
    }
}
