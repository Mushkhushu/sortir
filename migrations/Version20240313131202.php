<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240313131202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8D5E86FF FOREIGN KEY (etat_id) REFERENCES etat (id)');
        $this->addSql('CREATE INDEX IDX_488163E8D5E86FF ON sorties (etat_id)');
        $this->addSql('ALTER TABLE users ADD site_de_rattachement VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8D5E86FF');
        $this->addSql('DROP INDEX IDX_488163E8D5E86FF ON sorties');
        $this->addSql('ALTER TABLE `users` DROP site_de_rattachement');
    }
}
