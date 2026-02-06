# Audit de Sécurité ZenFleet V2 (Enterprise-Grade)

> [!IMPORTANT]
> **Niveau de Maturité Actuel:** 3.0/5 (Sécurité structurelle présente mais failles logiques critiques)
> **Cible:** 5/5 (Zero Trust, Enterprise-Ready)

## 0. Périmètre, Méthodologie et Modèle de Menaces

### 0.1. Périmètre (Scope)
- **Application:** ZenFleet (Laravel 12, Livewire 3, PostgreSQL 18, Docker)
- **Modules prioritaires:** RBAC/permissions, multi-tenant, imports/exports, affectations, gestion véhicules/chauffeurs.
- **Exclus:** Infrastructure (réseau/OS), SI externe (IdP), données historiques en prod.

### 0.2. Méthodologie
- **Revue statique** (code, routes, policies, middleware, imports).
- **Revue logique** (scénarios d’abus multi-tenant, RBAC, bypass).
- **Validation ciblée** (preuves techniques minimales, commandes reproductibles).

### 0.3. Modèle de menaces (résumé)
- **Acteurs:** utilisateur interne malveillant, utilisateur légitime mais curieux, attaquant externe ayant accès à un compte.
- **Actifs critiques:** données chauffeurs/véhicules, affectations, documents, organisations, permissions.
- **Surfaces d’attaque:** imports/exports, endpoints Livewire, policies/middleware, jobs/queues, storage.

### 0.4. Scoring (référence)
Chaque risque est évalué sur **Impact (I)**, **Exploitabilité (E)**, **Détectabilité (D)**, score 1-5.
Seuils: **Critique (≥4.5)**, **Élevé (≥3.5)**, **Moyen (≥2.5)**.

## 1. Vue d'ensemble des Risques Critiques

| Risque | Sévérité | Description | Statut |
| :--- | :---: | :--- | :--- |
| **Cross-Tenant Data Corruption** | **CRITIQUE** | Faille dans l'import des chauffeurs permettant d'écraser les données d'une autre organisation. | 🔴 À corriger immédiatement |
| **Inconsistent Authorization** | ÉLEVÉE | Mélange de conventions de nommage (espaces vs points) rendant l'audit difficile et les erreurs probables. | 🟠 À standardiser |
| **Hardcoded Role Logic** | MOYENNE | Logique dispersée basés sur des noms de rôles (chaînes de caractères) plutôt que des capacités. | 🟠 À refactoriser |

### 1.1. Preuves minimales et reproduction (résumé)
**Cross-Tenant Data Corruption**
- **Evidence technique:** `app/Livewire/Admin/Drivers/DriversImport.php#L373`
  - Recherche d’existant non scopée par `organization_id`.
- **Commande preuve:** `rg -n "license_number" app/Livewire/Admin/Drivers/DriversImport.php`
- **Reproduction:** importer un CSV avec un `license_number` appartenant à une autre org + option "mettre à jour".

**Inconsistent Authorization**
- **Evidence technique:** `app/Http/Middleware/EnterprisePermissionMiddleware.php#L29`, `app/Http/Middleware/EnterprisePermissionMiddleware.php#L58`
  - mix de notations `view vehicles` et `assignments.view`.
- **Commande preuve:** `rg -n "view vehicles|assignments\\.view" app/Http/Middleware/EnterprisePermissionMiddleware.php`

**Hardcoded Role Logic**
- **Evidence technique:** usages directs de rôles en chaînes.
  - `app/Policies/RepairRequestPolicy.php#L100` (`hasRole('Admin')`)
  - `app/Policies/RepairRequestPolicy.php#L133` (`hasRole('Supervisor')`)
  - `app/Policies/VehicleMileageReadingPolicy.php#L158` (`hasRole('Chauffeur')`)
- **Commande preuve:** `rg -n "hasRole\\(" app/Policies`

## 2. Plan d'Amélioration Stratégique

### Phase 1: Remédiation Immédiate (Sécurité des Données)
**Objectif:** Éliminer tout risque de fuite ou corruption de données inter-organisations.

1.  **Patch de Sécurité `DriversImport`:**
    *   Forcer le scope `organization_id` sur la recherche des doublons.
    *   Vérifier les permissions d'écriture sur l'objet trouvé via Policy.
2.  **Audit des autres imports:**
    *   Vérifier `VehicleImport` (déjà a priori sécurisé mais à confirmer).
    *   Vérifier `ImportExportService`.

### Phase 2: Standardisation du Contrôle d'Accès (RBAC V2)
**Objectif:** Migrer vers un modèle de permissions granulaire et prévisible.

1.  **Convention de Nommage Unique:** Adoption stricte de la notation `resource.action` (ex: `vehicles.view`, `drivers.create`).
2.  **Migration BDD:** Script pour renommer toutes les permissions existantes en base (`view vehicles` -> `vehicles.view`).
3.  **Refactor Code:** Mise à jour de `EnterprisePermissionMiddleware`, Policies, et Vues (Blade/Livewire).

### Phase 3: Durcissement (Hardening)
1.  **Strict Mode Middleware:** Le middleware de permission doit rejeter par défaut toute route non mappée explicitement (actuellement "Fail-Open" en dev, risqué).
2.  **Audit Logs Centralisés:** Implémenter un logging systématique des actions critiques (Création, Modif, Suppression, Export) avec contexte (IP, User, Org, Old/New Values).
3.  **Couches non couvertes:** jobs/queues, storage, exports async, routes Livewire.

## 3. Recommandations Techniques Détaillées

### 3.1. Standardisation des Permissions
Adopter le schéma : `{resource}.{action}`
*   `vehicles.index`, `vehicles.show`, `vehicles.create`, `vehicles.edit`, `vehicles.delete`
*   `assignments.create` (déjà conforme), `assignments.check-availability` (custom)

### 3.2. Sécurisation des Imports
Pattern obligatoire pour tout import :
```php
// Pattern Sécurisé
$existing = Model::where('unique_field', $value)
    ->where('organization_id', auth()->user()->organization_id) // OBLIGATOIRE
    ->first();
```

### 3.3. Gestion des Rôles
Remplacer les `hasRole('Admin')` éparpillés par des permissions de haut niveau ou des méthodes de service :
*   `$user->isAdmin()` (basé sur le rôle ou une permission `admin.access`)
*   Éviter les comparaisons de chaînes de caractères brutes.

## 4. Contrôles complémentaires requis (niveau expert)
- **Jobs/Queues:** vérifier que tous les jobs utilisent `organization_id` et n’emploient pas `withoutGlobalScopes()`.
- **Storage & documents:** vérifier isolation logique/physique par org (chemins, permissions, tokens).
- **Exports:** vérifier qu’ils respectent le scope d’organisation et la permission d’export.
- **Livewire:** vérifier les actions publiques et leur protection via policy/middleware.

## 5. Critères d’acceptation (DoD sécurité)
- Aucune requête de mise à jour/lecture cross‑tenant sans `organization_id`.
- Permissions standardisées par schéma unique `resource.action`.
- Toutes les routes sensibles mappées explicitement au middleware.
- Audit logs pour actions critiques avec contexte complet.

## 6. Limites connues
- Audit statique, pas de tests d’intrusion réseau.
- Requiert vérifications complémentaires sur jobs, storage, exports.
