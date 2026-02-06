# Rapport de Vérification de l'Audit de Sécurité

**Date:** 31 Janvier 2026
**Analyste:** Antigravity (Agent AI)
**Projet:** ZenFleet

## 0. Périmètre et Méthodologie de Vérification
- **Portée:** Vérification ciblée des assertions de `audit_securite.md` (RBAC, multi‑tenant, imports/exports).
- **Approche:** Revue statique + preuves minimales reproductibles.
- **Limites:** Pas d’audit réseau, pas de pentest dynamique.

## 1. Synthèse

L'analyse approfondie de la base de code confirme la majorité des points soulevés dans le document initial `audit_securite.md`, mais nuance leur gravité grâce à la présence de mécanismes de "Defense in Depth" (trait `BelongsToOrganization`, scopes globaux) qui n'avaient pas été pleinement identifiés par l'audit initial.

Cependant, une **faille critique de sécurité cross-tenant** a été découverte dans le module d'importation des chauffeurs (Livewire), confirmant le besoin urgent d'un plan de remédiation.

## 2. Vérification des Points de l'Audit

### 2.1. Gouvernance des Permissions et Rôles
*   **Confirmé:** Incohérence majeure dans le nommage des permissions. Le middleware `EnterprisePermissionMiddleware` mélange `view vehicles` (espaces) et `assignments.view` (notation par point).
*   **Confirmé:** Risque de fragmentation des rôles. Le code référence parfois `Supervisor` et potentiellement `Superviseur` (bien que la base de code PHP semble propre, le risque BDD existe).
*   **Nuancé:** La logique de "Super Admin bypass" est correctement centralisée dans le middleware et les policies.

### 2.2. Isolation Multi-Tenant (Data Leakage)
*   **Positif (Non identifié par l'audit):** Le trait `BelongsToOrganization` est présent sur les modèles critiques (`Vehicle`, `Driver`, `User`). Il applique un `Global Scope` qui filtre toutes les requêtes de lecture automatiquement par `organization_id`.
*   **Positif:** Le middleware `EnterprisePermissionMiddleware` et les contrôleurs (`AssignmentController`) utilisent correctement ce scope.
*   **Risque Confirmé:** Les imports/exports présentent des surfaces d'attaque.

### 2.3. Sécurité des Imports (NOUVELLE FAILLE CRITIQUE)
L'audit mentionnait un risque théorique sur les imports. L'analyse du code (`app/Livewire/Admin/Drivers/DriversImport.php`) a révélé une vulnérabilité concrète :

```php
// Faille dans DriversImport.php
$existing = Driver::where('license_number', $data['license_number'])->first();
```

**Impact:** Cette requête n'est PAS scopée par `organization_id`.
**Scénario d'attaque:** Un utilisateur malveillant de l'Organisation A peut importer un fichier CSV avec le numéro de permis d'un chauffeur de l'Organisation B. Si l'option "Mettre à jour les existants" est cochée, **il écrasera les données du chauffeur de l'Organisation B**, causant une corruption de données inter-organisations.

## 3. Preuves et Reproductibilité (checklist)

### 3.1. Evidence RBAC (incohérence de nommage)
- **Fichier:** `app/Http/Middleware/EnterprisePermissionMiddleware.php`
- **Lignes clés:** `#L29` (mention format espaces), `#L58` (permissions en notation point)
- **Commande preuve:** `rg -n "view vehicles|assignments\\.view" app/Http/Middleware/EnterprisePermissionMiddleware.php`
- **Critère attendu:** mélange de conventions confirmé.

### 3.2. Evidence Multi‑tenant (scopes)
- **Fichier:** `app/Models/Concerns/BelongsToOrganization.php`
- **Lignes clés:** `#L13` (boot), `#L17` (global scope), `#L25` (bypass Super Admin)
- **Commande preuve:** `sed -n '1,120p' app/Models/Concerns/BelongsToOrganization.php`
- **Critère attendu:** global scope par `organization_id` + bypass Super Admin.

### 3.3. Evidence Import Drivers (faille)
- **Fichier:** `app/Livewire/Admin/Drivers/DriversImport.php`
- **Ligne clé:** `#L373` (recherche d’existant sans `organization_id`)
- **Commande preuve:** `rg -n "license_number" app/Livewire/Admin/Drivers/DriversImport.php`
- **Critère attendu:** recherche non scopée par `organization_id`.

### 3.4. Evidence Hardcoded Roles
- **Fichiers:** `app/Policies/RepairRequestPolicy.php`, `app/Policies/VehicleMileageReadingPolicy.php`
- **Lignes clés:** `RepairRequestPolicy.php#L100`, `RepairRequestPolicy.php#L133`, `VehicleMileageReadingPolicy.php#L158`
- **Commande preuve:** `rg -n "hasRole\\(" app/Policies`
- **Critère attendu:** usage de rôles en chaînes (risque de fragmentation).

## 4. Limites et zones à vérifier
- Jobs/queues (possible bypass des scopes).
- Storage/exports (risque de fuite par fichiers).
- Requêtes raw ou `withoutGlobalScopes()` (à inspecter).

## 3. Autres Observations

*   **Export:** Les exports (`AssignmentController`, `ImportExportService`) utilisent correctement `auth()->user()->organization_id`.
*   **Création:** La création manuelle (`StoreDriverRequest`) force correctement l'ID de l'organisation.

## 4. Conclusion

La priorité absolue doit être donnée à la correction du module d'importation et à la standardisation du système de permissions pour éviter que de telles erreurs ne se reproduisent (fail-safe defaults).

## 5. Next Steps (pour audit expert)
- Enrichir la preuve par références de lignes et captures d’exécution.
- Élargir la vérification aux jobs/exports/storage/Livewire actions.
- Ajouter un scoring formel (impact/exploitabilité/détectabilité).
