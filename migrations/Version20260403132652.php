<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260403132652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP INDEX fk_activite_evenement ON activite');
        $this->addSql('ALTER TABLE activite CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE candidat DROP FOREIGN KEY fk_candidat_user');
        $this->addSql('ALTER TABLE candidat CHANGE niveau_etude niveau_etude VARCHAR(120) DEFAULT NULL, CHANGE experience experience INT NOT NULL');
        $this->addSql('DROP INDEX fk_candidature_offre ON candidature');
        $this->addSql('DROP INDEX uq_candidature_unique ON candidature');
        $this->addSql('ALTER TABLE candidature CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE cv_path cv_path VARCHAR(500) DEFAULT NULL, CHANGE cv_original_name cv_original_name VARCHAR(255) DEFAULT NULL, CHANGE cv_uploaded_at cv_uploaded_at DATETIME DEFAULT NULL, CHANGE match_updated_at match_updated_at DATETIME DEFAULT NULL, CHANGE signature_request_id signature_request_id VARCHAR(255) DEFAULT NULL, CHANGE contract_status contract_status VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE conge_tt DROP FOREIGN KEY fk_conge_employe');
        $this->addSql('DROP INDEX fk_conge_employe ON conge_tt');
        $this->addSql('ALTER TABLE conge_tt CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE document_path document_path VARCHAR(255) DEFAULT NULL, CHANGE ocr_verified ocr_verified TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE demande_service DROP FOREIGN KEY FK_DEMANDE_TYPE_SERVICE');
        $this->addSql('ALTER TABLE demande_service DROP FOREIGN KEY fk_demande_service_employe');
        $this->addSql('DROP INDEX FK_DEMANDE_TYPE_SERVICE ON demande_service');
        $this->addSql('DROP INDEX fk_demande_service_employe ON demande_service');
        $this->addSql('ALTER TABLE demande_service CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE etape_workflow etape_workflow VARCHAR(30) DEFAULT NULL, CHANGE date_derniere_etape date_derniere_etape DATE DEFAULT NULL, CHANGE priorite priorite VARCHAR(20) DEFAULT NULL, CHANGE deadline_reponse deadline_reponse DATE DEFAULT NULL, CHANGE sla_depasse sla_depasse TINYINT(1) DEFAULT NULL, CHANGE pdf_path pdf_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE employe DROP FOREIGN KEY fk_employe_user');
        $this->addSql('DROP INDEX matricule ON employe');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY fk_evenement_rh');
        $this->addSql('DROP INDEX fk_evenement_rh ON evenement');
        $this->addSql('ALTER TABLE evenement CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE image_url image_url VARCHAR(500) DEFAULT NULL, CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('DROP INDEX fk_participation_evenement ON event_participation');
        $this->addSql('DROP INDEX fk_participation_employe ON event_participation');
        $this->addSql('ALTER TABLE event_participation CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('DROP INDEX fk_offre_rh ON offre_emploi');
        $this->addSql('DROP INDEX uq_offre_unique ON offre_emploi');
        $this->addSql('ALTER TABLE offre_emploi CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE prime DROP FOREIGN KEY fk_prime_rh');
        $this->addSql('ALTER TABLE prime DROP FOREIGN KEY fk_prime_employe');
        $this->addSql('DROP INDEX fk_prime_rh ON prime');
        $this->addSql('DROP INDEX fk_prime_employe ON prime');
        $this->addSql('ALTER TABLE prime CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE montant montant NUMERIC(10, 0) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY fk_projet_responsable');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY fk_projet_rh');
        $this->addSql('DROP INDEX fk_projet_rh ON projet');
        $this->addSql('DROP INDEX fk_projet_responsable ON projet');
        $this->addSql('ALTER TABLE projet CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('DROP INDEX fk_rating_evenement ON rating');
        $this->addSql('DROP INDEX fk_rating_employe ON rating');
        $this->addSql('ALTER TABLE rating CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE commentaire commentaire LONGTEXT NOT NULL, CHANGE date_creation date_creation DATETIME NOT NULL');
        $this->addSql('DROP INDEX fk_reponse_demande ON reponse');
        $this->addSql('DROP INDEX fk_reponse_rh ON reponse');
        $this->addSql('DROP INDEX fk_reponse_employe ON reponse');
        $this->addSql('DROP INDEX fk_reponse_conge ON reponse');
        $this->addSql('ALTER TABLE reponse CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE commentaire commentaire VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE rh DROP FOREIGN KEY fk_rh_user');
        $this->addSql('ALTER TABLE salaire DROP FOREIGN KEY fk_salaire_employe');
        $this->addSql('ALTER TABLE salaire DROP FOREIGN KEY fk_salaire_rh');
        $this->addSql('DROP INDEX fk_salaire_rh ON salaire');
        $this->addSql('DROP INDEX fk_salaire_employe ON salaire');
        $this->addSql('ALTER TABLE salaire CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE montant montant NUMERIC(10, 0) NOT NULL, CHANGE date_paiement date_paiement DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY fk_tache_employe');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY fk_tache_prime');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY fk_tache_projet');
        $this->addSql('DROP INDEX fk_tache_projet ON tache');
        $this->addSql('DROP INDEX fk_tache_employe ON tache');
        $this->addSql('DROP INDEX fk_tache_prime ON tache');
        $this->addSql('ALTER TABLE tache CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE date_debut date_debut DATE DEFAULT NULL, CHANGE date_fin date_fin DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE type_service CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('DROP INDEX email ON users');
        $this->addSql('ALTER TABLE users CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE telephone telephone VARCHAR(40) DEFAULT NULL, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE role role VARCHAR(255) DEFAULT NULL, CHANGE avatar_path avatar_path VARCHAR(500) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE activite CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE description description TEXT DEFAULT NULL');
        $this->addSql('CREATE INDEX fk_activite_evenement ON activite (evenement_id)');
        $this->addSql('ALTER TABLE candidat CHANGE niveau_etude niveau_etude VARCHAR(120) DEFAULT \'NULL\', CHANGE experience experience INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE candidat ADD CONSTRAINT fk_candidat_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidature CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE cv_path cv_path VARCHAR(500) DEFAULT \'NULL\', CHANGE cv_original_name cv_original_name VARCHAR(255) DEFAULT \'NULL\', CHANGE cv_uploaded_at cv_uploaded_at DATETIME DEFAULT \'current_timestamp()\', CHANGE match_updated_at match_updated_at DATETIME DEFAULT \'NULL\', CHANGE signature_request_id signature_request_id VARCHAR(255) DEFAULT \'NULL\', CHANGE contract_status contract_status VARCHAR(50) DEFAULT \'\'\'NONE\'\'\'');
        $this->addSql('CREATE INDEX fk_candidature_offre ON candidature (offre_emploi_id)');
        $this->addSql('CREATE UNIQUE INDEX uq_candidature_unique ON candidature (candidat_id, offre_emploi_id)');
        $this->addSql('ALTER TABLE conge_tt CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE document_path document_path VARCHAR(255) DEFAULT \'NULL\', CHANGE ocr_verified ocr_verified TINYINT(1) DEFAULT 0');
        $this->addSql('ALTER TABLE conge_tt ADD CONSTRAINT fk_conge_employe FOREIGN KEY (employe_id) REFERENCES employe (user_id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX fk_conge_employe ON conge_tt (employe_id)');
        $this->addSql('ALTER TABLE demande_service CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE etape_workflow etape_workflow VARCHAR(30) DEFAULT \'\'\'SOUMISE\'\'\', CHANGE date_derniere_etape date_derniere_etape DATE DEFAULT \'NULL\', CHANGE priorite priorite VARCHAR(20) DEFAULT \'\'\'NORMAL\'\'\', CHANGE deadline_reponse deadline_reponse DATE DEFAULT \'NULL\', CHANGE sla_depasse sla_depasse TINYINT(1) DEFAULT 0, CHANGE pdf_path pdf_path VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE demande_service ADD CONSTRAINT FK_DEMANDE_TYPE_SERVICE FOREIGN KEY (type_service_id) REFERENCES type_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE demande_service ADD CONSTRAINT fk_demande_service_employe FOREIGN KEY (employe_id) REFERENCES employe (user_id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX FK_DEMANDE_TYPE_SERVICE ON demande_service (type_service_id)');
        $this->addSql('CREATE INDEX fk_demande_service_employe ON demande_service (employe_id)');
        $this->addSql('ALTER TABLE employe ADD CONSTRAINT fk_employe_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX matricule ON employe (matricule)');
        $this->addSql('ALTER TABLE evenement CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE image_url image_url VARCHAR(500) DEFAULT \'NULL\', CHANGE latitude latitude DOUBLE PRECISION DEFAULT \'NULL\', CHANGE longitude longitude DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT fk_evenement_rh FOREIGN KEY (rh_id) REFERENCES rh (user_id)');
        $this->addSql('CREATE INDEX fk_evenement_rh ON evenement (rh_id)');
        $this->addSql('ALTER TABLE event_participation CHANGE id id BIGINT AUTO_INCREMENT NOT NULL');
        $this->addSql('CREATE INDEX fk_participation_evenement ON event_participation (evenement_id)');
        $this->addSql('CREATE INDEX fk_participation_employe ON event_participation (employe_id)');
        $this->addSql('ALTER TABLE offre_emploi CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE description description TEXT NOT NULL');
        $this->addSql('CREATE INDEX fk_offre_rh ON offre_emploi (rh_id)');
        $this->addSql('CREATE UNIQUE INDEX uq_offre_unique ON offre_emploi (titre, localisation, type_contrat)');
        $this->addSql('ALTER TABLE prime CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE montant montant NUMERIC(12, 2) NOT NULL, CHANGE description description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE prime ADD CONSTRAINT fk_prime_rh FOREIGN KEY (rh_id) REFERENCES rh (user_id)');
        $this->addSql('ALTER TABLE prime ADD CONSTRAINT fk_prime_employe FOREIGN KEY (employe_id) REFERENCES employe (user_id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX fk_prime_rh ON prime (rh_id)');
        $this->addSql('CREATE INDEX fk_prime_employe ON prime (employe_id)');
        $this->addSql('ALTER TABLE projet CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE description description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT fk_projet_responsable FOREIGN KEY (responsable_employe_id) REFERENCES employe (user_id)');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT fk_projet_rh FOREIGN KEY (rh_id) REFERENCES rh (user_id)');
        $this->addSql('CREATE INDEX fk_projet_rh ON projet (rh_id)');
        $this->addSql('CREATE INDEX fk_projet_responsable ON projet (responsable_employe_id)');
        $this->addSql('ALTER TABLE rating CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE commentaire commentaire TEXT NOT NULL, CHANGE date_creation date_creation DATETIME DEFAULT \'current_timestamp()\' NOT NULL');
        $this->addSql('CREATE INDEX fk_rating_evenement ON rating (evenement_id)');
        $this->addSql('CREATE INDEX fk_rating_employe ON rating (employe_id)');
        $this->addSql('ALTER TABLE reponse CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE commentaire commentaire VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('CREATE INDEX fk_reponse_demande ON reponse (demande_service_id)');
        $this->addSql('CREATE INDEX fk_reponse_rh ON reponse (rh_id)');
        $this->addSql('CREATE INDEX fk_reponse_employe ON reponse (employe_id)');
        $this->addSql('CREATE INDEX fk_reponse_conge ON reponse (conge_tt_id)');
        $this->addSql('ALTER TABLE rh ADD CONSTRAINT fk_rh_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE salaire CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE montant montant NUMERIC(12, 2) NOT NULL, CHANGE date_paiement date_paiement DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE salaire ADD CONSTRAINT fk_salaire_employe FOREIGN KEY (employe_id) REFERENCES employe (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE salaire ADD CONSTRAINT fk_salaire_rh FOREIGN KEY (rh_id) REFERENCES rh (user_id)');
        $this->addSql('CREATE INDEX fk_salaire_rh ON salaire (rh_id)');
        $this->addSql('CREATE INDEX fk_salaire_employe ON salaire (employe_id)');
        $this->addSql('ALTER TABLE tache CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE date_debut date_debut DATE DEFAULT \'NULL\', CHANGE date_fin date_fin DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT fk_tache_employe FOREIGN KEY (employe_id) REFERENCES employe (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT fk_tache_prime FOREIGN KEY (prime_id) REFERENCES prime (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT fk_tache_projet FOREIGN KEY (projet_id) REFERENCES projet (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX fk_tache_projet ON tache (projet_id)');
        $this->addSql('CREATE INDEX fk_tache_employe ON tache (employe_id)');
        $this->addSql('CREATE INDEX fk_tache_prime ON tache (prime_id)');
        $this->addSql('ALTER TABLE type_service CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE description description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE telephone telephone VARCHAR(40) DEFAULT \'NULL\', CHANGE adresse adresse VARCHAR(255) DEFAULT \'NULL\', CHANGE role role VARCHAR(255) DEFAULT \'NULL\', CHANGE avatar_path avatar_path VARCHAR(500) DEFAULT \'NULL\'');
        $this->addSql('CREATE UNIQUE INDEX email ON users (email)');
    }
}
