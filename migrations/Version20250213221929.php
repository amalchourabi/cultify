<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250213221929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955284FD025');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955284FD025');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955284FD025 FOREIGN KEY (id_e) REFERENCES event (id_e) ON DELETE CASCADE');
        $this->addSql('DROP INDEX id_e ON reservation');
        $this->addSql('CREATE INDEX IDX_42C84955284FD025 ON reservation (id_e)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955284FD025 FOREIGN KEY (id_e) REFERENCES event (id_e)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955284FD025');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955284FD025');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955284FD025 FOREIGN KEY (id_e) REFERENCES event (id_e)');
        $this->addSql('DROP INDEX idx_42c84955284fd025 ON reservation');
        $this->addSql('CREATE INDEX id_e ON reservation (id_e)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955284FD025 FOREIGN KEY (id_e) REFERENCES event (id_e) ON DELETE CASCADE');
    }
}
