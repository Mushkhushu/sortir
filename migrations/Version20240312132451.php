<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312132451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8E09255E3 FOREIGN KEY (organizator_id) REFERENCES `users` (id)');
        $this->addSql('CREATE INDEX IDX_488163E8E09255E3 ON sorties (organizator_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8E09255E3');
        $this->addSql('DROP INDEX IDX_488163E8E09255E3 ON sorties');
    }
}
