<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303112938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, num_tel VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, gender VARCHAR(255) DEFAULT NULL, datedenaissance DATETIME NOT NULL, profilepicture VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', montant_apayer DOUBLE PRECISION DEFAULT NULL, ocr_data JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contenu_multi_media ADD id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contenu_multi_media ADD CONSTRAINT FK_B3AE5FD36B3CA4B FOREIGN KEY (id_user) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_B3AE5FD36B3CA4B ON contenu_multi_media (id_user)');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D979F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D9EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA76B3CA4B FOREIGN KEY (id_user) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_3BAE0AA76B3CA4B ON event (id_user)');
        $this->addSql('ALTER TABLE question ADD id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id_quiz)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_B6F7494E6B3CA4B ON question (id_user)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA923C1CC488 FOREIGN KEY (contenu_id) REFERENCES contenu_multi_media (id_contenu)');
        $this->addSql('ALTER TABLE reclamation ADD id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064046B3CA4B FOREIGN KEY (id_user) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_CE6064046B3CA4B ON reclamation (id_user)');
        $this->addSql('ALTER TABLE reponse ADD id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7D672A9F3 FOREIGN KEY (id_reclamation) REFERENCES reclamation (id_reclamation)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC76B3CA4B FOREIGN KEY (id_user) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5FB6DEC76B3CA4B ON reponse (id_user)');
        $this->addSql('ALTER TABLE reservation ADD id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955284FD025 FOREIGN KEY (id_e) REFERENCES event (id_e)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849556B3CA4B FOREIGN KEY (id_user) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_42C849556B3CA4B ON reservation (id_user)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contenu_multi_media DROP FOREIGN KEY FK_B3AE5FD36B3CA4B');
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D979F37AE5');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA76B3CA4B');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E6B3CA4B');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064046B3CA4B');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC76B3CA4B');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849556B3CA4B');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP INDEX IDX_B3AE5FD36B3CA4B ON contenu_multi_media');
        $this->addSql('ALTER TABLE contenu_multi_media DROP id_user');
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D9EFB9C8A5');
        $this->addSql('DROP INDEX IDX_3BAE0AA76B3CA4B ON event');
        $this->addSql('ALTER TABLE event DROP id_user');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E853CD175');
        $this->addSql('DROP INDEX IDX_B6F7494E6B3CA4B ON question');
        $this->addSql('ALTER TABLE question DROP id_user');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA923C1CC488');
        $this->addSql('DROP INDEX IDX_CE6064046B3CA4B ON reclamation');
        $this->addSql('ALTER TABLE reclamation DROP id_user');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7D672A9F3');
        $this->addSql('DROP INDEX IDX_5FB6DEC76B3CA4B ON reponse');
        $this->addSql('ALTER TABLE reponse DROP id_user');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955284FD025');
        $this->addSql('DROP INDEX IDX_42C849556B3CA4B ON reservation');
        $this->addSql('ALTER TABLE reservation DROP id_user');
    }
}
