<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240313132841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sorties DROP ville, DROP code_postal, DROP longitude, DROP latitude, DROP rue');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sorties ADD ville VARCHAR(255) NOT NULL, ADD code_postal INT NOT NULL, ADD longitude INT NOT NULL, ADD latitude INT NOT NULL, ADD rue VARCHAR(255) NOT NULL');
    }
}
