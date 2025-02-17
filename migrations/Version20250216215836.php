<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216215836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reponse DROP priorite, CHANGE id_reponse id_reponse INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id_reponse)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reponse MODIFY id_reponse INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON reponse');
        $this->addSql('ALTER TABLE reponse ADD priorite VARCHAR(255) NOT NULL, CHANGE id_reponse id_reponse INT NOT NULL');
    }
}
