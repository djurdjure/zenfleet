# 🏭 ZenFleet Factories & Seeders

## 📋 Résumé des Modifications

Ce document détaille les factories et seeders créés pour générer des données de test complètes pour ZenFleet.

## 🏗️ Factories Mises à Jour

### 1. OrganizationFactory
**Fichier**: `database/factories/OrganizationFactory.php`

**Fonctionnalités ajoutées**:
- ✅ **Données algériennes complètes** : NIF, AI, NIS, registre de commerce
- ✅ **Wilayas algériennes** : Liste complète des 48 wilayas
- ✅ **Formes juridiques algériennes** : SARL, SPA, SNC, EURL, EI
- ✅ **Adresses réalistes** : Coordonnées GPS Algérie, codes postaux
- ✅ **Responsable légal** : Nom, NIN, fonction, email
- ✅ **Paramètres localisés** : Timezone Africa/Algiers, devise DZD, français
- ✅ **States personnalisés** : `active()`, `enterprise()`, `sme()`, `pending()`

**Méthodes utilitaires**:
- `generateNIF()` - Génère un NIF algérien 15 chiffres
- `generateAI()` - Génère un AI algérien 14 chiffres
- `generateNIS()` - Génère un NIS algérien 15 chiffres
- `generateTradeRegister()` - Format: "WW/YY-XXXXXX B SS"
- `generateNIN()` - Génère un NIN algérien 18 chiffres

### 2. UserFactory
**Fichier**: `database/factories/UserFactory.php`

**Fonctionnalités ajoutées**:
- ✅ **UUID unique** pour chaque utilisateur
- ✅ **Informations complètes** : téléphone, adresse, date de naissance
- ✅ **Données professionnelles** : ID employé, date d'embauche, poste
- ✅ **Paramètres utilisateur** : timezone, langue, format date/heure
- ✅ **Sécurité** : 2FA, dernière connexion, tentatives échouées
- ✅ **States spécialisés** par rôle :
  - `superAdmin()` - Super administrateur système
  - `admin()` - Administrateur organisation
  - `fleetManager()` - Gestionnaire de flotte
  - `supervisor()` - Superviseur opérations
  - `driver()` - Chauffeur avec permis de conduire
  - `withTwoFactor()` - Avec 2FA activé
  - `forOrganization()` - Pour une organisation spécifique

## 🌱 Seeders Créés

### 1. ZenFleetRolesPermissionsSeeder
**Fichier**: `database/seeders/ZenFleetRolesPermissionsSeeder.php`

**Permissions créées** (65 permissions):
- 🏢 **Organisations** : view, create, edit, delete, manage_settings, etc.
- 👥 **Utilisateurs** : view, create, edit, delete, manage_roles, etc.
- 🚗 **Véhicules** : view, create, edit, delete, assign_driver, etc.
- 🚚 **Chauffeurs** : view, create, edit, delete, assign_vehicle, etc.
- 📊 **Rapports** : view, create, export, financial, operational, compliance
- 🔧 **Maintenance** : view, create, edit, delete, schedule, approve
- ⛽ **Carburant** : view, create, edit, delete, reports, manage_cards
- 🚚 **Trajets** : view, create, edit, delete, assign, track, reports
- 🏪 **Fournisseurs** : view, create, edit, delete, manage_contracts
- 📄 **Documents** : view, create, edit, delete, approve, download
- ⚙️ **Paramètres** : view, edit, system, notifications, integrations
- 🔍 **Audit** : view, export, delete
- 💰 **Facturation** : view, create, edit, export
- 🌐 **API** : access, manage_keys
- 🚨 **Alertes** : view, create, edit, delete, send notifications
- 🗺️ **Géolocalisation** : view, real_time, history, geofences
- ⚠️ **Super Admin** : system access, global management

**Rôles créés** (8 rôles):
1. **Super Admin** - Accès total système (toutes permissions)
2. **Admin** - Gestionnaire organisation (45+ permissions)
3. **Gestionnaire Flotte** - Gestion opérationnelle (30+ permissions)
4. **Superviseur** - Supervision et contrôle (15+ permissions)
5. **Chauffeur** - Accès limité conduite (6 permissions)
6. **Comptable** - Gestion financière (12+ permissions)
7. **Mécanicien** - Maintenance technique (8+ permissions)
8. **Analyste** - Rapports et analyses (15+ permissions)

### 2. DatabaseSeeder (Principal)
**Fichier**: `database/seeders/DatabaseSeeder.php`

**Données créées**:

#### Organisation ZenFleet (Principale)
- **Super Admin** : `superadmin@zenfleet.dz` / `password`
- Données légales algériennes complètes
- Informations responsable légal

#### Organisations de Test (Mode développement)

**1. TransAlger SARL (Enterprise - Équipe complète)**
- **Admin** : `admin@transalger.dz`
- **Gestionnaire Flotte** : `flotte@transalger.dz`
- **2 Superviseurs** : `superviseur1@transalger.dz`, `superviseur2@transalger.dz`
- **8 Chauffeurs** : `chauffeur1@transalger.dz` → `chauffeur8@transalger.dz`
- **Comptable** : `comptable@transalger.dz`
- **Mécanicien** : `mecanicien@transalger.dz`
- **Analyste** : `analyste@transalger.dz`
- **Total** : 15 utilisateurs + 15 véhicules + 8 fournisseurs

**2. LogistiqueOran SPA (Professional - Équipe moyenne)**
- **Admin** : `admin@logistiqueoran.dz`
- **Gestionnaire Flotte** : `flotte@logistiqueoran.dz`
- **Superviseur** : `superviseur@logistiqueoran.dz`
- **3 Chauffeurs** : `chauffeur1@logistiqueoran.dz` → `chauffeur3@logistiqueoran.dz`
- **Comptable** : `comptable@logistiqueoran.dz`
- **Total** : 7 utilisateurs + 8 véhicules + 5 fournisseurs

**3. Construction Constantine EURL (Basic - Équipe réduite)**
- **Admin** : `admin@construction.dz`
- **Gestionnaire Flotte** : `flotte@construction.dz`
- **Superviseur** : `superviseur@construction.dz`
- **2 Chauffeurs** : `chauffeur1@construction.dz`, `chauffeur2@construction.dz`
- **Total** : 5 utilisateurs + 5 véhicules + 3 fournisseurs

**4. Organisations supplémentaires**
- **Transport Annaba** (Status: pending)
- **Express Béjaïa** (Status: suspended)

## 🔐 Comptes de Test

| Email | Mot de passe | Rôle | Organisation |
|-------|--------------|------|--------------|
| `superadmin@zenfleet.dz` | `password` | Super Admin | ZenFleet |
| `admin@transalger.dz` | `password` | Admin | TransAlger |
| `flotte@transalger.dz` | `password` | Gestionnaire Flotte | TransAlger |
| `superviseur1@transalger.dz` | `password` | Superviseur | TransAlger |
| `chauffeur1@transalger.dz` | `password` | Chauffeur | TransAlger |
| `comptable@transalger.dz` | `password` | Comptable | TransAlger |
| `mecanicien@transalger.dz` | `password` | Mécanicien | TransAlger |
| `analyste@transalger.dz` | `password` | Analyste | TransAlger |

*Tous les comptes utilisent le mot de passe : `password`*

## 🚀 Utilisation

### Exécuter les seeders
```bash
# Réinitialiser et créer toutes les données
php artisan migrate:fresh --seed

# Exécuter seulement les seeders
php artisan db:seed

# Exécuter un seeder spécifique
php artisan db:seed --class=ZenFleetRolesPermissionsSeeder
```

### Environnements
- **Production** : Seule l'organisation ZenFleet + Super Admin
- **Development/Local** : Toutes les organisations de test + utilisateurs

## 📊 Statistiques Générées

### Organisations
- **1** organisation principale (ZenFleet)
- **5** organisations de test
- **3** statuts différents (active, pending, suspended)
- **3** plans d'abonnement (enterprise, professional, basic)

### Utilisateurs
- **1** Super Admin
- **28** utilisateurs de test
- **8** rôles différents testés
- **Données algériennes** complètes (NIN, téléphones +213, etc.)

### Données Associées
- **28** véhicules total
- **16** fournisseurs total
- **Tous liés** aux organisations respectives

## ✅ Validations et Conformité

### Données Algériennes
- ✅ **Wilayas** : Code à 2 chiffres (01-48)
- ✅ **NIF** : 15 chiffres
- ✅ **AI** : 14 chiffres
- ✅ **NIS** : 15 chiffres
- ✅ **NIN** : 18 chiffres
- ✅ **Registre Commerce** : Format "WW/YY-XXXXXX B SS"
- ✅ **Téléphones** : Format +213 XX XX XX XX XX
- ✅ **Adresses** : Villes algériennes réelles

### Sécurité
- ✅ **Mots de passe** hachés avec bcrypt
- ✅ **UUIDs** uniques pour tous les modèles
- ✅ **Emails** uniques
- ✅ **Rôles et permissions** Spatie fonctionnels
- ✅ **2FA** optionnel configuré

## 🔧 Extensions Possibles

1. **Ajout de véhicules** avec chauffeurs assignés
2. **Trajets en cours** avec données GPS
3. **Historique de maintenance**
4. **Factures et paiements**
5. **Documents uploadés** (PDFs de test)
6. **Alertes et notifications** actives

---

*🎯 Les factories et seeders sont maintenant prêts pour les tests de développement et la démonstration du système ZenFleet!*