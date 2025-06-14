<?php

namespace Database\Seeders\Maintenance;

use App\Models\Maintenance\MaintenanceType;
use Illuminate\Database\Seeder;

class MaintenanceTypeSeeder extends Seeder
{
    /**
     * Exécute les seeds pour la table des types de maintenance.
     */
    public function run(): void
    {
        // La méthode firstOrCreate garantit que nous ne créerons pas de doublons
        // si le seeder est exécuté plusieurs fois.

        // --- Types techniques (maintenance & entretien courant) ---
        $this->createType(
            'Vidange moteur', 
            'Changement de l\'huile moteur uniquement.'
        );
        $this->createType(
            'Vidange moteur complète', 
            'Changement de l\'huile moteur et du filtre à huile.'
        );
        $this->createType(
            'Vidange boîte de vitesse', 
            'Changement de l\'huile de la boîte de vitesse (manuelle ou automatique).'
        );
        $this->createType(
            'Courroie de distribution', 
            'Vérification ou remplacement du kit de distribution.'
        );
        $this->createType(
            'Courroie d\'accessoires', 
            'Vérification ou remplacement de la courroie d\'accessoires (poly-V).'
        );
        $this->createType(
            'Pneumatiques', 
            'Contrôle ou remplacement des pneus (pression, usure, géométrie).'
        );
        $this->createType(
            'Système de freinage', 
            'Contrôle ou remplacement des disques, plaquettes, et purge du liquide de frein.'
        );
        $this->createType(
            'Système électrique', 
            'Contrôle de la batterie, de l\'alternateur et du démarreur.'
        );
        $this->createType(
            'Filtres', 
            'Remplacement des filtres à air, à carburant, et d\'habitacle (pollen).'
        );
        $this->createType(
            'Nettoyage FAP/DPF', 
            'Nettoyage ou régénération du filtre à particules.'
        );
        $this->createType(
            'Système de climatisation', 
            'Recharge de gaz, contrôle d\'étanchéité, et remplacement du filtre déshydrateur.'
        );

        // --- Types administratifs & réglementaires ---
        $this->createType(
            'Contrôle technique', 
            'Inspection réglementaire périodique obligatoire.'
        );
        $this->createType(
            'Vignette automobile', 
            'Paiement annuel de la taxe de circulation.'
        );
        $this->createType(
            'Permis de circuler', 
            'Renouvellement ou mise à jour de la carte grise / permis de circuler.'
        );
        $this->createType(
            'Assurance automobile', 
            'Paiement ou renouvellement de la police d\'assurance (RC, tous risques, etc.).'
        );
        $this->createType(
            'Assurance marchandises', 
            'Paiement ou renouvellement de l\'assurance pour les biens transportés.'
        );
        $this->createType(
            'Autorisation de mise en circulation (AMC)', 
            'Contrôles spécifiques pour les véhicules de transport de marchandises ou de personnes.'
        );
        $this->createType(
            'Révision générale constructeur', 
            'Entretien complet suivant les préconisations du constructeur.'
        );

        $this->command->info('Maintenance types seeded successfully.');
    }

    /**
     * Méthode privée pour simplifier la création.
     */
    private function createType(string $name, string $description): void
    {
        MaintenanceType::firstOrCreate(
            ['name' => $name], 
            ['description' => $description]
        );
    }
}
