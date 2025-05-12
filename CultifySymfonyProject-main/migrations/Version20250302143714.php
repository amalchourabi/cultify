<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302143714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE association (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, montant_desire DOUBLE PRECISION DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, contact VARCHAR(255) DEFAULT NULL, but VARCHAR(255) DEFAULT NULL, site_web VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contenu_multi_media (id_contenu INT AUTO_INCREMENT NOT NULL, titre_media VARCHAR(255) NOT NULL, text_media VARCHAR(255) NOT NULL, photo_media VARCHAR(255) DEFAULT NULL, categorie_media VARCHAR(255) NOT NULL, date_media DATETIME DEFAULT NULL, PRIMARY KEY(id_contenu)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE don (id INT AUTO_INCREMENT NOT NULL, id_user_id INT DEFAULT NULL, association_id INT NOT NULL, montant DOUBLE PRECISION DEFAULT NULL, donor_type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_F8F081D979F37AE5 (id_user_id), INDEX IDX_F8F081D9EFB9C8A5 (association_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id_e INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_e DATE NOT NULL, organisation VARCHAR(255) NOT NULL, capacite INT NOT NULL, nbplaces INT NOT NULL, categorie VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, image VARCHAR(255) NOT NULL, longitude DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, PRIMARY KEY(id_e)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id_question INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, text_question VARCHAR(255) NOT NULL, response_prop1 VARCHAR(255) NOT NULL, response_prop2 VARCHAR(255) NOT NULL, response_prop3 VARCHAR(255) NOT NULL, response_correct VARCHAR(255) NOT NULL, INDEX IDX_B6F7494E853CD175 (quiz_id), PRIMARY KEY(id_question)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz (id_quiz INT AUTO_INCREMENT NOT NULL, contenu_id INT DEFAULT NULL, titre_quiz VARCHAR(255) NOT NULL, date_quiz DATETIME DEFAULT NULL, score_quiz INT NOT NULL, reponse_choisit VARCHAR(255) NOT NULL, INDEX IDX_A412FA923C1CC488 (contenu_id), PRIMARY KEY(id_quiz)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id_reclamation INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, priorite VARCHAR(255) NOT NULL, piece_jointe VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id_reclamation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id_reponse INT AUTO_INCREMENT NOT NULL, id_reclamation INT NOT NULL, date_reponse DATE NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, offre VARCHAR(255) NOT NULL, piece_jointe VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_5FB6DEC7D672A9F3 (id_reclamation), PRIMARY KEY(id_reponse)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id_r INT AUTO_INCREMENT NOT NULL, id_e INT NOT NULL, etat VARCHAR(255) NOT NULL, date_r DATE NOT NULL, theme VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, nb_tickets INT NOT NULL, INDEX IDX_42C84955284FD025 (id_e), PRIMARY KEY(id_r)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D979F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D9EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id_quiz)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA923C1CC488 FOREIGN KEY (contenu_id) REFERENCES contenu_multi_media (id_contenu)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7D672A9F3 FOREIGN KEY (id_reclamation) REFERENCES reclamation (id_reclamation)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955284FD025 FOREIGN KEY (id_e) REFERENCES event (id_e)');
        $this->addSql('ALTER TABLE user ADD ocr_data JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D979F37AE5');
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D9EFB9C8A5');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E853CD175');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA923C1CC488');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7D672A9F3');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955284FD025');
        $this->addSql('DROP TABLE association');
        $this->addSql('DROP TABLE contenu_multi_media');
        $this->addSql('DROP TABLE don');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('ALTER TABLE user DROP ocr_data');
    }
}
