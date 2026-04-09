<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Seed: TypeService — 10 catégories avec leurs items détaillés.
 */
final class Version20260409200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed TypeService : 10 catégories (Matériel informatique, Véhicule, Fournitures, etc.) avec leurs sous-types.';
    }

    public function up(Schema $schema): void
    {
        $items = [
            // ── 1. Matériel informatique ──────────────────────────────────
            ['categorie' => 'Matériel informatique', 'nom' => 'PC portable',           'description' => 'Ordinateur portable pour usage professionnel'],
            ['categorie' => 'Matériel informatique', 'nom' => 'PC fixe',               'description' => 'Ordinateur de bureau pour poste de travail fixe'],
            ['categorie' => 'Matériel informatique', 'nom' => 'Écran / Moniteur',      'description' => 'Écran supplémentaire ou de remplacement'],
            ['categorie' => 'Matériel informatique', 'nom' => 'Souris',                'description' => 'Souris filaire ou sans fil'],
            ['categorie' => 'Matériel informatique', 'nom' => 'Clavier',               'description' => 'Clavier filaire ou sans fil'],
            ['categorie' => 'Matériel informatique', 'nom' => 'Casque audio / Micro',  'description' => 'Casque pour réunions à distance et conférences'],
            ['categorie' => 'Matériel informatique', 'nom' => 'Webcam',                'description' => 'Caméra pour visioconférence'],
            ['categorie' => 'Matériel informatique', 'nom' => 'Disque dur externe',    'description' => 'Stockage de données externe portable'],
            ['categorie' => 'Matériel informatique', 'nom' => 'Clé USB',               'description' => 'Mémoire flash USB'],
            ['categorie' => 'Matériel informatique', 'nom' => 'Imprimante / Scanner',  'description' => 'Périphérique d\'impression ou de numérisation'],

            // ── 2. Véhicule de service ────────────────────────────────────
            ['categorie' => 'Véhicule de service', 'nom' => 'Véhicule de pool',        'description' => 'Demande de réservation d\'un véhicule partagé'],
            ['categorie' => 'Véhicule de service', 'nom' => 'Véhicule de fonction',    'description' => 'Attribution d\'un véhicule de fonction personnel'],
            ['categorie' => 'Véhicule de service', 'nom' => 'Carte carburant',         'description' => 'Carte de carburant liée au véhicule de service'],
            ['categorie' => 'Véhicule de service', 'nom' => 'Entretien / Révision',    'description' => 'Demande d\'entretien ou de révision du véhicule'],
            ['categorie' => 'Véhicule de service', 'nom' => 'Assurance véhicule',      'description' => 'Souscription ou renouvellement d\'assurance'],

            // ── 3. Fournitures de bureau ──────────────────────────────────
            ['categorie' => 'Fournitures de bureau', 'nom' => 'Stylos / Crayons',      'description' => 'Fournitures d\'écriture courantes'],
            ['categorie' => 'Fournitures de bureau', 'nom' => 'Ramettes de papier',    'description' => 'Papier A4 pour imprimante'],
            ['categorie' => 'Fournitures de bureau', 'nom' => 'Classeurs / Chemises',  'description' => 'Classeurs, chemises cartonnées, intercalaires'],
            ['categorie' => 'Fournitures de bureau', 'nom' => 'Post-it / Bloc-notes',  'description' => 'Adhésifs repositionnables et blocs de notes'],
            ['categorie' => 'Fournitures de bureau', 'nom' => 'Toner / Cartouche',     'description' => 'Cartouche d\'encre ou toner pour imprimante'],
            ['categorie' => 'Fournitures de bureau', 'nom' => 'Agrafeuse / Perfo',     'description' => 'Agrafeuse, perforatrice, agrafes'],
            ['categorie' => 'Fournitures de bureau', 'nom' => 'Cahiers / Répertoires', 'description' => 'Cahiers, répertoires de bureau'],

            // ── 4. Télétravail ────────────────────────────────────────────
            ['categorie' => 'Télétravail', 'nom' => 'Accès VPN',                       'description' => 'Activation ou dépannage de la connexion VPN'],
            ['categorie' => 'Télétravail', 'nom' => 'Équipement domicile',             'description' => 'Fourniture de matériel pour travailler à domicile'],
            ['categorie' => 'Télétravail', 'nom' => 'Forfait internet',                'description' => 'Remboursement ou prise en charge du forfait internet'],
            ['categorie' => 'Télétravail', 'nom' => 'Chaise ergonomique',              'description' => 'Chaise de bureau ergonomique pour le domicile'],
            ['categorie' => 'Télétravail', 'nom' => 'Avenant télétravail',             'description' => 'Formalisation contractuelle du télétravail'],

            // ── 5. Formation professionnelle ──────────────────────────────
            ['categorie' => 'Formation professionnelle', 'nom' => 'Formation externe', 'description' => 'Formation dispensée par un organisme externe'],
            ['categorie' => 'Formation professionnelle', 'nom' => 'Formation interne', 'description' => 'Formation assurée en interne par l\'entreprise'],
            ['categorie' => 'Formation professionnelle', 'nom' => 'Certification',     'description' => 'Passage d\'une certification ou d\'un examen professionnel'],
            ['categorie' => 'Formation professionnelle', 'nom' => 'E-learning',        'description' => 'Accès à une plateforme de formation en ligne'],
            ['categorie' => 'Formation professionnelle', 'nom' => 'Séminaire / Conférence', 'description' => 'Participation à un séminaire ou une conférence métier'],
            ['categorie' => 'Formation professionnelle', 'nom' => 'Bilan de compétences', 'description' => 'Évaluation et orientation professionnelle'],

            // ── 6. Maintenance / Réparation ───────────────────────────────
            ['categorie' => 'Maintenance / Réparation', 'nom' => 'Réparation PC / Laptop',   'description' => 'Dépannage ou réparation d\'un ordinateur'],
            ['categorie' => 'Maintenance / Réparation', 'nom' => 'Réparation imprimante',     'description' => 'Dépannage ou réparation d\'imprimante'],
            ['categorie' => 'Maintenance / Réparation', 'nom' => 'Maintenance réseau',        'description' => 'Intervention sur l\'infrastructure réseau'],
            ['categorie' => 'Maintenance / Réparation', 'nom' => 'Remplacement matériel',     'description' => 'Remplacement d\'un équipement défaillant'],
            ['categorie' => 'Maintenance / Réparation', 'nom' => 'Intervention sur site',     'description' => 'Visite technique d\'un prestataire sur site'],

            // ── 7. Accès logiciel / Licence ───────────────────────────────
            ['categorie' => 'Accès logiciel / Licence', 'nom' => 'Microsoft Office 365',     'description' => 'Licence ou accès Microsoft 365 (Word, Excel, Teams…)'],
            ['categorie' => 'Accès logiciel / Licence', 'nom' => 'Adobe Creative Suite',     'description' => 'Licence Adobe (Photoshop, Illustrator, Acrobat…)'],
            ['categorie' => 'Accès logiciel / Licence', 'nom' => 'Antivirus / Sécurité',     'description' => 'Licence logiciel antivirus ou de sécurité'],
            ['categorie' => 'Accès logiciel / Licence', 'nom' => 'Logiciel métier',           'description' => 'Accès à un outil spécifique au métier (ERP, CRM…)'],
            ['categorie' => 'Accès logiciel / Licence', 'nom' => 'Accès GitHub / DevOps',    'description' => 'Accès aux outils de développement (GitHub, GitLab, Jira…)'],
            ['categorie' => 'Accès logiciel / Licence', 'nom' => 'Création de compte',       'description' => 'Création ou réinitialisation d\'un compte applicatif'],

            // ── 8. Mission / Déplacement ──────────────────────────────────
            ['categorie' => 'Mission / Déplacement', 'nom' => 'Réservation transport',       'description' => 'Train, avion ou bus pour déplacement professionnel'],
            ['categorie' => 'Mission / Déplacement', 'nom' => 'Réservation hôtel',           'description' => 'Hébergement pour mission ou déplacement'],
            ['categorie' => 'Mission / Déplacement', 'nom' => 'Billet d\'avion',              'description' => 'Achat ou remboursement de billet d\'avion'],
            ['categorie' => 'Mission / Déplacement', 'nom' => 'Per diem / Indemnité',        'description' => 'Indemnité journalière de déplacement'],
            ['categorie' => 'Mission / Déplacement', 'nom' => 'Location de voiture',         'description' => 'Location d\'un véhicule pour une mission'],

            // ── 9. Remboursement de frais ─────────────────────────────────
            ['categorie' => 'Remboursement de frais', 'nom' => 'Frais de repas',              'description' => 'Remboursement des repas en déplacement ou en mission'],
            ['categorie' => 'Remboursement de frais', 'nom' => 'Frais kilométriques',         'description' => 'Indemnisation kilométrique pour utilisation du véhicule personnel'],
            ['categorie' => 'Remboursement de frais', 'nom' => 'Note de frais transport',    'description' => 'Remboursement des frais de transport (métro, taxi, bus…)'],
            ['categorie' => 'Remboursement de frais', 'nom' => 'Frais de représentation',     'description' => 'Frais engagés lors de réceptions ou d\'événements professionnels'],
            ['categorie' => 'Remboursement de frais', 'nom' => 'Avance sur frais',            'description' => 'Demande d\'avance avant une mission ou un déplacement'],

            // ── 10. Autre demande ─────────────────────────────────────────
            ['categorie' => 'Autre demande', 'nom' => 'Badge / Accès bâtiment',               'description' => 'Création, remplacement ou désactivation d\'un badge d\'accès'],
            ['categorie' => 'Autre demande', 'nom' => 'Clés / Accès bureau',                   'description' => 'Fourniture ou duplication de clés de bureau'],
            ['categorie' => 'Autre demande', 'nom' => 'Téléphonie mobile',                     'description' => 'Attribution ou remplacement d\'un téléphone professionnel'],
            ['categorie' => 'Autre demande', 'nom' => 'Demande personnalisée',                 'description' => 'Toute autre demande non catégorisée'],
        ];

        foreach ($items as $item) {
            $nom         = $this->connection->quote($item['nom']);
            $categorie   = $this->connection->quote($item['categorie']);
            $description = $this->connection->quote($item['description']);

            $this->addSql(
                "INSERT INTO type_service (nom, categorie, description)
                 SELECT $nom, $categorie, $description
                 WHERE NOT EXISTS (
                     SELECT 1 FROM type_service WHERE nom = $nom AND categorie = $categorie
                 )"
            );
        }
    }

    public function down(Schema $schema): void
    {
        // Supprime uniquement les lignes insérées par cette migration
        $categories = [
            'Matériel informatique',
            'Véhicule de service',
            'Fournitures de bureau',
            'Télétravail',
            'Formation professionnelle',
            'Maintenance / Réparation',
            'Accès logiciel / Licence',
            'Mission / Déplacement',
            'Remboursement de frais',
            'Autre demande',
        ];
        foreach ($categories as $cat) {
            $quoted = $this->connection->quote($cat);
            $this->addSql("DELETE FROM type_service WHERE categorie = $quoted");
        }
    }
}
