<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312154115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sorties (id INT AUTO_INCREMENT NOT NULL, organizator_id INT UNSIGNED NOT NULL, etat_id INT NOT NULL, nom VARCHAR(255) NOT NULL, date DATETIME NOT NULL, duree INT DEFAULT NULL, lieu VARCHAR(255) NOT NULL, nbr_personne INT DEFAULT NULL, note VARCHAR(255) NOT NULL, date_limite DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_488163E8E09255E3 (organizator_id), INDEX IDX_488163E8D5E86FF (etat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `users` (id INT UNSIGNED AUTO_INCREMENT NOT NULL, username VARCHAR(50) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(50) DEFAULT NULL, last_name VARCHAR(50) DEFAULT NULL, phone_number VARCHAR(20) DEFAULT NULL, mail VARCHAR(180) NOT NULL, picture VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E95126AC48 (mail), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_sorties (user_id INT UNSIGNED NOT NULL, sorties_id INT NOT NULL, INDEX IDX_108C7DE2A76ED395 (user_id), INDEX IDX_108C7DE215DFCFB2 (sorties_id), PRIMARY KEY(user_id, sorties_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8E09255E3 FOREIGN KEY (organizator_id) REFERENCES `users` (id)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8D5E86FF FOREIGN KEY (etat_id) REFERENCES etat (id)');
        $this->addSql('ALTER TABLE user_sorties ADD CONSTRAINT FK_108C7DE2A76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_sorties ADD CONSTRAINT FK_108C7DE215DFCFB2 FOREIGN KEY (sorties_id) REFERENCES sorties (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8E09255E3');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8D5E86FF');
        $this->addSql('ALTER TABLE user_sorties DROP FOREIGN KEY FK_108C7DE2A76ED395');
        $this->addSql('ALTER TABLE user_sorties DROP FOREIGN KEY FK_108C7DE215DFCFB2');
        $this->addSql('DROP TABLE sorties');
        $this->addSql('DROP TABLE `users`');
        $this->addSql('DROP TABLE user_sorties');
    }
}
