<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314083910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lieu ADD ville_id INT NOT NULL, DROP ville');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('CREATE INDEX IDX_2F577D59A73F0036 ON lieu (ville_id)');
        $this->addSql('ALTER TABLE sorties ADD lieu_id INT DEFAULT NULL, DROP lieu');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E86AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id)');
        $this->addSql('CREATE INDEX IDX_488163E86AB213CC ON sorties (lieu_id)');
        $this->addSql('ALTER TABLE users DROP site_de_rattachement');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59A73F0036');
        $this->addSql('DROP INDEX IDX_2F577D59A73F0036 ON lieu');
        $this->addSql('ALTER TABLE lieu ADD ville VARCHAR(255) NOT NULL, DROP ville_id');
        $this->addSql('ALTER TABLE `users` ADD site_de_rattachement VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E86AB213CC');
        $this->addSql('DROP INDEX IDX_488163E86AB213CC ON sorties');
        $this->addSql('ALTER TABLE sorties ADD lieu VARCHAR(255) NOT NULL, DROP lieu_id');
    }
}
