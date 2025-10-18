# 📋 ZenFleet - Inventaire Complet des Vues Blade

**Version:** 1.0.0
**Date:** 18 Octobre 2025
**Total de fichiers:** 217 vues Blade

---

## 📖 Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Layouts](#layouts)
3. [Pages Admin](#pages-admin)
4. [Composants](#composants)
5. [Livewire](#livewire)
6. [Pages Publiques](#pages-publiques)
7. [Priorisation pour Refonte](#priorisation-pour-refonte)

---

## Vue d'ensemble

### Statistiques

| Catégorie | Nombre | % |
|-----------|--------|---|
| Pages Admin | 102 | 47% |
| Composants | 55 | 25% |
| Livewire | 25 | 12% |
| Layouts | 10 | 5% |
| Auth | 7 | 3% |
| Dashboard | 6 | 3% |
| Profile | 4 | 2% |
| Public | 2 | 1% |
| Pagination | 6 | 3% |
| **TOTAL** | **217** | **100%** |

### Répartition par Domaine Fonctionnel

| Domaine | Fichiers | Priorité |
|---------|----------|----------|
| Véhicules | 12 | P0 - Critique |
| Chauffeurs | 12 | P0 - Critique |
| Affectations | 10 | P0 - Critique |
| Maintenance | 15 | P1 - Haute |
| Kilométrage | 5 | P1 - Haute |
| Documents | 7 | P2 - Moyenne |
| Organisations | 5 | P2 - Moyenne |
| Utilisateurs/Rôles | 6 | P2 - Moyenne |
| Demandes de réparation | 6 | P1 - Haute |
| Handovers | 4 | P2 - Moyenne |
| Expenses | 2 | P3 - Basse |

---

## Layouts

### Layouts Principaux

```
resources/views/layouts/
├── admin.blade.php                     # P0 - Layout admin principal (ancien)
├── admin/
│   ├── app.blade.php                   # P0 - Layout enterprise
│   ├── catalyst.blade.php              # P0 - Layout référence (ACTUEL)
│   └── partials/
│       ├── header.blade.php            # P0 - Header avec navigation
│       ├── footer.blade.php            # P3 - Footer
│       └── notifications.blade.php     # P2 - Centre de notifications
├── app.blade.php                       # P3 - Layout public
├── guest.blade.php                     # P1 - Layout auth/guest
└── navigation.blade.php                # P3 - Navigation (Breeze)
```

**Notes:**
- ⭐ `catalyst.blade.php` est le layout de référence
- Harmoniser tous les layouts avec le design system Catalyst

---

## Pages Admin

### Dashboard (6 fichiers)

```
resources/views/admin/
├── dashboard.blade.php                              # P0 - Dashboard principal
├── dashboard-enterprise.blade.php                   # P0 - Dashboard enterprise
└── dashboard/
    ├── index.blade.php                              # P0 - Vue index
    ├── admin.blade.php                              # P1 - Dashboard admin
    ├── admin-old.blade.php                          # P3 - À supprimer
    ├── super-admin.blade.php                        # P1 - Dashboard super-admin
    ├── fleet-manager.blade.php                      # P1 - Dashboard gestionnaire
    └── supervisor.blade.php                         # P1 - Dashboard superviseur
```

**Notes:**
- Plusieurs dashboards selon rôles utilisateur
- `admin-old.blade.php` est obsolète → Supprimer après migration

---

### Véhicules (12 fichiers)

```
resources/views/admin/vehicles/
├── index.blade.php                     # P0 - Liste véhicules
├── create.blade.php                    # P0 - Créer véhicule
├── edit.blade.php                      # P0 - Éditer véhicule
├── show.blade.php                      # P0 - Détails véhicule
├── archived.blade.php                  # P2 - Véhicules archivés
├── enterprise-index.blade.php          # P0 - Liste enterprise
├── enterprise-create.blade.php         # P0 - Créer enterprise
├── enterprise-edit.blade.php           # P0 - Éditer enterprise
├── enterprise-show.blade.php           # P0 - Détails enterprise
├── import.blade.php                    # P2 - Import CSV
└── import-results.blade.php            # P2 - Résultats import
```

**Analyse:**
- Doublons: versions standard + enterprise
- **Action:** Unifier les vues standard et enterprise avec le design system

---

### Chauffeurs (12 fichiers)

```
resources/views/admin/drivers/
├── index.blade.php                     # P0 - Liste chauffeurs
├── create.blade.php                    # P0 - Créer chauffeur
├── edit.blade.php                      # P0 - Éditer chauffeur
├── edit-fixed.blade.php                # P1 - Version fixed (bug fix)
├── show.blade.php                      # P0 - Détails chauffeur
├── archived.blade.php                  # P2 - Chauffeurs archivés
├── enterprise-index.blade.php          # P0 - Liste enterprise
├── index-enterprise.blade.php          # P0 - Doublon ?
├── import.blade.php                    # P2 - Import CSV
├── import-results.blade.php            # P2 - Résultats import
└── partials/
    ├── step1-personal.blade.php        # P0 - Wizard étape 1
    ├── step2-professional.blade.php    # P0 - Wizard étape 2
    ├── step2-professional-fixed.blade.php # P1 - Version fixed
    ├── step3-license.blade.php         # P0 - Wizard étape 3
    └── step4-account.blade.php         # P0 - Wizard étape 4
```

**Analyse:**
- Wizard multi-étapes pour création chauffeur
- Doublons: `edit.blade.php` vs `edit-fixed.blade.php`
- Doublons: `enterprise-index.blade.php` vs `index-enterprise.blade.php`
- **Action:** Nettoyer doublons, unifier avec design system

---

### Affectations (10 fichiers)

```
resources/views/admin/assignments/
├── index.blade.php                     # P0 - Liste affectations
├── index-simple.blade.php              # P1 - Version simple
├── index-enterprise.blade.php          # P0 - Version enterprise
├── create.blade.php                    # P0 - Créer affectation
├── create-enterprise.blade.php         # P0 - Créer enterprise
├── edit.blade.php                      # P0 - Éditer affectation
└── gantt.blade.php                     # P1 - Vue Gantt
```

**Analyse:**
- Plusieurs variantes: simple, enterprise, gantt
- **Action:** Unifier avec design system, conserver flexibilité variantes

---

### Maintenance (15 fichiers)

```
resources/views/admin/maintenance/
├── dashboard.blade.php                 # P1 - Dashboard maintenance
├── dashboard-enterprise.blade.php      # P1 - Dashboard enterprise
├── alerts/
│   └── index.blade.php                 # P1 - Alertes maintenance
├── operations/
│   ├── index.blade.php                 # P1 - Liste opérations
│   └── create.blade.php                # P1 - Créer opération
├── plans/
│   ├── index.blade.php                 # P1 - Liste plans
│   └── create.blade.php                # P1 - Créer plan
├── providers/
│   ├── index.blade.php                 # P2 - Liste fournisseurs
│   └── create.blade.php                # P2 - Créer fournisseur
├── reports/
│   └── index.blade.php                 # P2 - Rapports maintenance
├── schedules/
│   ├── index.blade.php                 # P1 - Liste planifications
│   ├── create.blade.php                # P1 - Créer planification
│   ├── edit.blade.php                  # P1 - Éditer planification
│   └── show.blade.php                  # P1 - Détails planification
├── surveillance/
│   └── index.blade.php                 # P2 - Surveillance
└── types/
    ├── index.blade.php                 # P2 - Types maintenance
    └── create.blade.php                # P2 - Créer type
```

**Analyse:**
- Module complexe avec 7 sous-sections
- **Action:** Appliquer design system uniformément

---

### Kilométrage (5 fichiers)

```
resources/views/admin/mileage-readings/
├── index.blade.php                     # P1 - ⭐ Liste (référence tables)
├── history.blade.php                   # P1 - Historique
└── update.blade.php                    # P1 - Mettre à jour
```

**Notes:**
- ⭐ `index.blade.php` est la référence pour le style des tables
- Utilisé comme modèle dans components-demo.blade.php

---

### Demandes de Réparation (6 fichiers)

```
resources/views/admin/repair-requests/
├── index.blade.php                     # P1 - Liste demandes
├── create.blade.php                    # P1 - Créer demande
├── show.blade.php                      # P1 - Détails demande
└── partials/
    └── modals.blade.php                # P1 - Modals (approbation, etc.)
```

**Analyse:**
- Utilise modals pour actions (approbation, rejet)
- **Action:** Migrer vers <x-modal> du design system

---

### Documents (7 fichiers)

```
resources/views/admin/documents/
├── index.blade.php                     # P2 - Liste documents
├── create.blade.php                    # P2 - Créer document
├── edit.blade.php                      # P2 - Éditer document
└── _form.blade.php                     # P2 - Partial formulaire
```

```
resources/views/admin/document_categories/
├── index.blade.php                     # P2 - Liste catégories
├── create.blade.php                    # P2 - Créer catégorie
└── edit.blade.php                      # P2 - Éditer catégorie
```

**Analyse:**
- Module de gestion documentaire
- **Action:** Standardiser formulaires avec design system

---

### Organisations (5 fichiers)

```
resources/views/admin/organizations/
├── index.blade.php                     # P2 - Liste organisations
├── create.blade.php                    # P2 - Créer organisation
├── edit.blade.php                      # P2 - Éditer organisation
└── show.blade.php                      # P2 - Détails organisation
```

**Analyse:**
- Multi-tenant: gestion des organisations
- **Action:** Appliquer design system

---

### Utilisateurs et Rôles (6 fichiers)

```
resources/views/admin/users/
├── index.blade.php                     # P2 - Liste utilisateurs
├── create.blade.php                    # P2 - Créer utilisateur
├── edit.blade.php                      # P2 - Éditer utilisateur
└── permissions.blade.php               # P2 - Gestion permissions

resources/views/admin/roles/
├── index.blade.php                     # P2 - Liste rôles
├── edit.blade.php                      # P2 - Éditer rôle
└── permissions.blade.php               # P2 - Gestion permissions rôle
```

**Analyse:**
- Gestion RBAC (Role-Based Access Control)
- **Action:** Tables de permissions avec design system

---

### Handovers (Remises de Véhicules) (4 fichiers)

```
resources/views/admin/handovers/vehicles/
├── create.blade.php                    # P2 - Créer remise
├── edit.blade.php                      # P2 - Éditer remise
├── show.blade.php                      # P2 - Détails remise
└── pdf.blade.php                       # P3 - Export PDF
```

**Analyse:**
- Formulaires de remise/restitution véhicules
- **Action:** Appliquer design system sauf PDF

---

### Fournisseurs (5 fichiers)

```
resources/views/admin/suppliers/
├── index.blade.php                     # P2 - Liste fournisseurs
├── create.blade.php                    # P2 - Créer fournisseur
└── edit.blade.php                      # P2 - Éditer fournisseur

resources/views/admin/suppliers-enterprise/
├── index.blade.php                     # P2 - Liste enterprise
└── create.blade.php                    # P2 - Créer enterprise
```

**Analyse:**
- Doublons: versions standard + enterprise
- **Action:** Unifier avec design system

---

### Dépenses Véhicules (2 fichiers)

```
resources/views/admin/vehicle-expenses/
├── index.blade.php                     # P3 - Liste dépenses
└── create.blade.php                    # P3 - Créer dépense
```

---

### Autres Pages Admin

```
resources/views/admin/
├── components-demo.blade.php           # ⭐ P0 - RÉFÉRENCE ULTIME
├── alerts/
│   └── index.blade.php                 # P2 - Centre alertes
├── expenses/
│   └── index.blade.php                 # P3 - Dépenses générales
├── planning/
│   └── index.blade.php                 # P1 - Planning
├── placeholder/
│   └── index.blade.php                 # P3 - Placeholder (dev)
└── sanctions/
    └── index.blade.php                 # P2 - Sanctions chauffeurs
```

**Notes:**
- ⭐ `components-demo.blade.php` est la référence absolue du design system

---

## Composants

### Composants du Design System (14 fichiers)

```
resources/views/components/
├── button.blade.php                    # ✅ Design System - Button
├── input.blade.php                     # ✅ Design System - Input
├── select.blade.php                    # ✅ Design System - Select
├── textarea.blade.php                  # ✅ Design System - Textarea
├── tom-select.blade.php                # ✅ Design System - TomSelect
├── datepicker.blade.php                # ✅ Design System - Datepicker
├── time-picker.blade.php               # ✅ Design System - TimePicker
├── alert.blade.php                     # ✅ Design System - Alert
├── badge.blade.php                     # ✅ Design System - Badge
├── modal.blade.php                     # ✅ Design System - Modal
├── breadcrumb.blade.php                # 🔄 À standardiser
├── stat-card.blade.php                 # 🔄 À standardiser
├── dropdown.blade.php                  # 🔄 À standardiser
└── dropdown-link.blade.php             # 🔄 À standardiser
```

**Légende:**
- ✅ Conforme au Design System Flowbite-inspired
- 🔄 À standardiser avec le Design System

---

### Composants Breeze/Auth (8 fichiers)

```
resources/views/components/
├── application-logo.blade.php          # P3 - Logo application
├── auth-session-status.blade.php       # P1 - Statut session auth
├── danger-button.blade.php             # 🔄 Doublon de Button variant="danger"
├── primary-button.blade.php            # 🔄 Doublon de Button variant="primary"
├── secondary-button.blade.php          # 🔄 Doublon de Button variant="secondary"
├── text-input.blade.php                # 🔄 Doublon de Input
├── input-label.blade.php               # 🔄 Intégré dans Input/Select/Textarea
└── input-error.blade.php               # 🔄 Intégré dans Input (prop error)
```

**Actions:**
- ❌ Supprimer doublons après migration vers Design System
- ✅ Conserver pour compatibilité Breeze temporairement

---

### Composants Navigation (5 fichiers)

```
resources/views/components/
├── nav-link.blade.php                  # P2 - Lien navigation
├── responsive-nav-link.blade.php       # P2 - Lien responsive
├── sub-nav-link.blade.php              # P2 - Sous-navigation
├── sidebar/
│   ├── sidebar-link.blade.php          # P1 - Lien sidebar
│   ├── sidebar-sub-link.blade.php      # P1 - Sous-lien sidebar
│   └── sidebar-group.blade.php         # P1 - Groupe sidebar
```

**Action:** Standardiser avec design system Catalyst

---

### Composants Métier (10 fichiers)

```
resources/views/components/
├── assignment-status-badge.blade.php   # P1 - Badge statut affectation
├── status-badge.blade.php              # P1 - Badge statut générique
├── priority-badge.blade.php            # P1 - Badge priorité
├── repair-status-badge.blade.php       # P1 - Badge statut réparation
├── handover-status-switcher.blade.php  # P2 - Switcher remise
├── date-picker.blade.php               # 🔄 Ancien datepicker
├── datetime-picker.blade.php           # 🔄 Ancien datetime picker
├── toast.blade.php                     # P2 - Notifications toast
├── flash.blade.php                     # P2 - Flash messages
└── flash-message.blade.php             # P2 - Flash messages (doublon?)
```

**Actions:**
- Migrer badges vers composant Badge unifié
- Supprimer anciens pickers (remplacés par datepicker/time-picker)

---

### Composants Formulaires (8 fichiers)

```
resources/views/components/
├── organization-form.blade.php         # P2 - Formulaire organisation
├── organization-form-enterprise.blade.php # P2 - Version enterprise
├── organization-form-algeria.blade.php # P3 - Version Algérie
├── algeria-form-field.blade.php        # P3 - Champs spécifiques Algérie
├── vehicle-form-field.blade.php        # P1 - Champs véhicule
├── form-error-summary.blade.php        # P2 - Résumé erreurs
```

**Action:** Migrer vers composants Design System (Input, Select, etc.)

---

### Composants Enterprise (6 fichiers)

```
resources/views/components/enterprise/
├── button.blade.php                    # 🔄 Doublon Button
├── input.blade.php                     # 🔄 Doublon Input
├── modal.blade.php                     # 🔄 Doublon Modal
├── card.blade.php                      # 🔄 À standardiser
├── filter-panel.blade.php              # P2 - Panel filtres
└── toast.blade.php                     # 🔄 Doublon Toast
```

**Actions:**
- ❌ Supprimer doublons (button, input, modal, toast)
- ✅ Migrer card et filter-panel vers Design System

---

### Composants Divers (4 fichiers)

```
resources/views/components/
├── enterprise-notification.blade.php   # P2 - Notifications enterprise
├── enterprise-table.blade.php          # P1 - Tables enterprise
└── enterprise-widget.blade.php         # P2 - Widgets enterprise
```

**Action:** Standardiser avec design system

---

## Livewire

### Livewire Admin (17 fichiers)

```
resources/views/livewire/admin/
├── assignment/
│   └── create-assignment.blade.php     # P0 - Créer affectation
├── driver-sanction-index.blade.php     # P2 - Sanctions chauffeurs
├── maintenance/
│   └── schedule-manager.blade.php      # P1 - Gestionnaire planification
├── mileage-readings-index.blade.php    # P1 - Kilométrage (Livewire)
├── organization-table.blade.php        # P2 - Table organisations
├── permission-matrix.blade.php         # P2 - Matrice permissions
├── repair-request-create.blade.php     # P1 - Créer demande réparation
├── repair-request-manager-kanban.blade.php # P1 - Kanban réparations
├── repair-request-modals.blade.php     # P1 - Modals réparations
├── repair-request-modals-enterprise.blade.php # P1 - Version enterprise
├── update-vehicle-mileage.blade.php    # P1 - Mettre à jour kilométrage
├── user-permission-manager.blade.php   # P2 - Gestion permissions user
└── vehicle-mileage-history.blade.php   # P1 - Historique kilométrage
```

---

### Livewire Autres (8 fichiers)

```
resources/views/livewire/
├── assignments/
│   ├── assignment-table.blade.php      # P0 - Table affectations
│   ├── assignment-form.blade.php       # P0 - Formulaire affectations
│   └── assignment-gantt.blade.php      # P1 - Gantt affectations
├── assignment-table.blade.php          # 🔄 Doublon?
├── assignment-form.blade.php           # 🔄 Doublon?
├── assignment-gantt.blade.php          # 🔄 Doublon?
├── organization-manager.blade.php      # P2 - Gestionnaire organisations
├── organization-manager-error.blade.php # P3 - Erreur organisations
├── repair-requests-index.blade.php     # P1 - Index réparations
├── repair-requests-table.blade.php     # P1 - Table réparations
└── repair-request-approval-modal.blade.php # P1 - Modal approbation
```

**Action:** Nettoyer doublons `assignments/` vs racine

---

## Pages Publiques

### Authentification (7 fichiers)

```
resources/views/auth/
├── login.blade.php                     # P1 - Connexion
├── login-enterprise.blade.php          # P1 - Connexion enterprise
├── register.blade.php                  # P1 - Inscription
├── forgot-password.blade.php           # P2 - Mot de passe oublié
├── reset-password.blade.php            # P2 - Réinitialiser mot de passe
├── confirm-password.blade.php          # P3 - Confirmer mot de passe
└── verify-email.blade.php              # P3 - Vérifier email
```

**Action:** Appliquer design system (layout guest)

---

### Profile (4 fichiers)

```
resources/views/profile/
├── edit.blade.php                      # P2 - Éditer profil
└── partials/
    ├── update-profile-information-form.blade.php # P2
    ├── update-password-form.blade.php  # P2
    └── delete-user-form.blade.php      # P3
```

---

### Dashboards Publics (6 fichiers)

```
resources/views/dashboard/
├── driver.blade.php                    # P1 - Dashboard chauffeur
├── driver-old.blade.php                # P3 - À supprimer
├── error.blade.php                     # P3 - Page erreur

resources/views/driver/
└── repair-requests/
    └── index.blade.php                 # P1 - Demandes réparation chauffeur
```

---

### Autres (2 fichiers)

```
resources/views/
├── welcome.blade.php                   # P3 - Page d'accueil
└── dashboard.blade.php                 # P2 - Dashboard principal
```

---

## Priorisation pour Refonte

### Phase 0 - Critique (P0) - 25 fichiers

**Layouts (3):**
- `layouts/admin/catalyst.blade.php` ⭐
- `layouts/admin/app.blade.php`
- `layouts/admin/partials/header.blade.php`

**Pages (22):**
- `admin/components-demo.blade.php` ⭐ RÉFÉRENCE
- `admin/dashboard.blade.php`
- `admin/dashboard-enterprise.blade.php`
- `admin/dashboard/index.blade.php`
- `admin/vehicles/index.blade.php`
- `admin/vehicles/create.blade.php`
- `admin/vehicles/edit.blade.php`
- `admin/vehicles/show.blade.php`
- `admin/vehicles/enterprise-*.blade.php` (4 fichiers)
- `admin/drivers/index.blade.php`
- `admin/drivers/create.blade.php`
- `admin/drivers/edit.blade.php`
- `admin/drivers/show.blade.php`
- `admin/drivers/partials/step*.blade.php` (4 fichiers)
- `admin/assignments/index.blade.php`
- `admin/assignments/create.blade.php`
- `admin/assignments/edit.blade.php`
- Livewire: `assignment-table`, `assignment-form`, `create-assignment`

**Timeline:** Semaine 1-2 (10 jours)

---

### Phase 1 - Haute Priorité (P1) - 45 fichiers

**Pages:**
- Maintenance (dashboard, schedules, operations, alerts)
- Mileage readings (index, history, update)
- Repair requests (index, create, show, modals)
- Dashboards par rôle (admin, super-admin, fleet-manager)
- Auth (login, register)
- Livewire: Kanban, Gantt, tables diverses

**Composants:**
- Sidebar (links, groups)
- Status badges
- Navigation

**Timeline:** Semaine 3-4 (10 jours)

---

### Phase 2 - Moyenne Priorité (P2) - 60 fichiers

**Pages:**
- Documents et catégories
- Organisations
- Utilisateurs et rôles
- Handovers
- Suppliers
- Alerts, Sanctions
- Profile

**Composants:**
- Enterprise (card, filter-panel, notification, table, widget)
- Forms (organization, vehicle-form-field, error-summary)
- Toast, Flash, Breadcrumb

**Timeline:** Semaine 5 (5 jours)

---

### Phase 3 - Basse Priorité (P3) - 20 fichiers

**Pages:**
- Vehicle expenses
- Handover PDF
- Welcome page
- Auth secondaire (verify-email, confirm-password)
- Anciens fichiers (driver-old, admin-old)

**Composants:**
- Algeria-specific
- Application logo
- Footer

**Timeline:** Cleanup final

---

### À Supprimer Après Migration - 15 fichiers

```
# Doublons Breeze
components/danger-button.blade.php
components/primary-button.blade.php
components/secondary-button.blade.php
components/text-input.blade.php
components/input-label.blade.php
components/input-error.blade.php

# Doublons Enterprise
components/enterprise/button.blade.php
components/enterprise/input.blade.php
components/enterprise/modal.blade.php
components/enterprise/toast.blade.php

# Anciens pickers
components/date-picker.blade.php
components/datetime-picker.blade.php

# Doublons Livewire
livewire/assignment-table.blade.php (si doublon confirmé)
livewire/assignment-form.blade.php (si doublon confirmé)
livewire/assignment-gantt.blade.php (si doublon confirmé)

# Fichiers obsolètes
admin/dashboard/admin-old.blade.php
dashboard/driver-old.blade.php
```

---

## Checklist de Migration par Vue

Pour chaque vue Blade, appliquer:

### ✅ Checklist Standard

- [ ] Remplacer Heroicons par Iconify
- [ ] Utiliser composants Design System:
  - [ ] `<x-button>` au lieu de HTML brut
  - [ ] `<x-input>` au lieu de `<input>`
  - [ ] `<x-select>` au lieu de `<select>`
  - [ ] `<x-textarea>` au lieu de `<textarea>`
  - [ ] `<x-tom-select>` pour dropdowns avec recherche
  - [ ] `<x-datepicker>` pour dates
  - [ ] `<x-time-picker>` pour heures
  - [ ] `<x-alert>` pour alertes
  - [ ] `<x-badge>` pour badges
  - [ ] `<x-modal>` pour modals
- [ ] Appliquer classes Tailwind standardisées:
  - [ ] Sections: `bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700`
  - [ ] Grids: `grid grid-cols-1 md:grid-cols-2 gap-6`
  - [ ] Tables: Style kilométrage (voir COMPOSANTS_REFERENCE.md)
- [ ] Support dark mode complet (`dark:` variants)
- [ ] Responsive design (breakpoints sm, md, lg, xl, 2xl)
- [ ] Accessibilité:
  - [ ] Labels pour tous inputs
  - [ ] ARIA attributes
  - [ ] Focus states
  - [ ] Keyboard navigation
- [ ] Tester:
  - [ ] Affichage desktop
  - [ ] Affichage mobile
  - [ ] Dark mode
  - [ ] Validation formulaires

---

## Pagination (6 fichiers)

```
resources/views/vendor/pagination/
├── tailwind.blade.php                  # P1 - Pagination Tailwind
├── simple-tailwind.blade.php           # P2
├── default.blade.php                   # P3
├── simple-default.blade.php            # P3
├── bootstrap-4.blade.php               # P3
├── bootstrap-5.blade.php               # P3
├── simple-bootstrap-4.blade.php        # P3
├── simple-bootstrap-5.blade.php        # P3
└── semantic-ui.blade.php               # P3
```

**Action:** Standardiser `tailwind.blade.php` avec design system

---

## Statistiques de Refonte

### Par Phase

| Phase | Fichiers | Jours | % Total |
|-------|----------|-------|---------|
| P0 - Critique | 25 | 10 | 12% |
| P1 - Haute | 45 | 10 | 21% |
| P2 - Moyenne | 60 | 5 | 28% |
| P3 - Basse | 20 | 3 | 9% |
| Composants | 55 | 5 | 25% |
| Livewire | 25 | 5 | 12% |
| **TOTAL** | **230** | **38** | **100%** |

### Durée Estimée

- **Phase 0 (P0):** 10 jours ouvrés
- **Phase 1 (P1):** 10 jours ouvrés
- **Phase 2 (P2):** 5 jours ouvrés
- **Phase 3 (P3):** 3 jours ouvrés
- **Composants:** 5 jours (parallèle)
- **Livewire:** 5 jours (parallèle)
- **Tests & QA:** 5 jours
- **TOTAL:** ~38 jours ouvrés (~8 semaines)

---

## Commandes Utiles

### Compter fichiers par catégorie

```bash
# Compter pages admin
find resources/views/admin -name "*.blade.php" -not -path "*/vendor/*" | wc -l

# Compter composants
find resources/views/components -name "*.blade.php" | wc -l

# Compter Livewire
find resources/views/livewire -name "*.blade.php" | wc -l

# Rechercher utilisation d'anciens composants
grep -r "<x-text-input" resources/views/admin
grep -r "<x-primary-button" resources/views/admin
```

### Rechercher Heroicons

```bash
# Trouver toutes utilisations Heroicons
grep -r "heroicon-" resources/views/admin --include="*.blade.php"

# Compter occurrences
grep -r "heroicon-" resources/views/admin --include="*.blade.php" | wc -l
```

---

## Fichiers de Référence

### Design System
- ⭐ `resources/views/admin/components-demo.blade.php` - RÉFÉRENCE ULTIME
- ⭐ `resources/views/admin/mileage-readings/index.blade.php` - Tables référence
- ⭐ `resources/views/layouts/admin/catalyst.blade.php` - Layout référence

### Documentation
- `PLAN_REFONTE_DESIGN_ZENFLEET.md` - Plan global
- `COMPOSANTS_REFERENCE.md` - Documentation composants
- `INVENTAIRE_VUES.md` (ce fichier) - Inventaire vues
- `HEROICONS_MAPPING.md` - Mapping icônes (à créer)
- `STYLES_GUIDE.md` - Guide styles (à créer)
- `ALPINE_PATTERNS.md` - Patterns Alpine.js (à créer)

---

## Notes de Migration

### Patterns Récurrents à Standardiser

1. **Tables avec icônes véhicules:**
   - Utiliser pattern de `mileage-readings/index.blade.php`
   - Icône véhicule: `bg-blue-100` + `text-blue-600`

2. **Formulaires multi-étapes:**
   - Drivers: 4 étapes (personal, professional, license, account)
   - Utiliser tabs ou stepper avec design system

3. **Import CSV:**
   - Véhicules et chauffeurs ont import
   - Standardiser formulaire upload + résultats

4. **Versions Enterprise vs Standard:**
   - Véhicules, chauffeurs, affectations, etc.
   - Unifier avec flags conditionnels si possible

5. **Livewire Tables:**
   - Appliquer style kilométrage
   - Pagination Tailwind standardisée

---

**Maintenu par:** ZenFleet Development Team
**Dernière mise à jour:** 18 Octobre 2025
