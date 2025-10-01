# 🔧 Module Maintenance Enterprise-Grade - ZenFleet

## 📋 Vue d'Ensemble

Le module Maintenance Enterprise-Grade de ZenFleet est une solution complète et professionnelle pour la gestion avancée de la maintenance de flotte. Développé avec les dernières technologies Laravel 12, Livewire 3, et une architecture multi-tenant stricte.

## 🏗️ Architecture Technique

### Stack Technologique
- **Backend:** Laravel 12 + PHP 8.3
- **Frontend:** Livewire 3 + Alpine.js + Tailwind CSS
- **Base de données:** PostgreSQL 16 avec indexation optimisée
- **API:** Sanctum + RESTful architecture
- **Monitoring:** Health checks intégrés
- **Exports:** Maatwebsite Excel avec multi-feuilles

### Architecture Multi-Tenant
- Isolation stricte par `organization_id`
- Scopes globaux automatiques
- Sécurité renforcée au niveau des modèles
- Permissions granulaires

## 📊 Structure de Base de Données

### Tables Principales

#### 1. `maintenance_types` - Types de Maintenance
```sql
- id: bigint (PK)
- organization_id: bigint (FK, indexed)
- name: varchar(255)
- description: text
- category: enum('preventive', 'corrective', 'inspection', 'revision')
- is_recurring: boolean
- default_interval_km: integer
- default_interval_days: integer
- estimated_duration_minutes: integer
- estimated_cost: decimal(12,2)
- is_active: boolean (default: true)
- created_at, updated_at: timestamps
```

#### 2. `maintenance_providers` - Fournisseurs
```sql
- id: bigint (PK)
- organization_id: bigint (FK, indexed)
- name: varchar(255)
- company_name: varchar(255)
- email: varchar(255)
- phone: varchar(50)
- address: text
- city: varchar(100)
- postal_code: varchar(20)
- specialties: json (array de spécialités)
- rating: decimal(3,2) (0-5)
- is_active: boolean
- created_at, updated_at: timestamps
```

#### 3. `maintenance_schedules` - Planifications
```sql
- id: bigint (PK)
- organization_id: bigint (FK, indexed)
- vehicle_id: bigint (FK, indexed)
- maintenance_type_id: bigint (FK)
- next_due_date: date
- next_due_mileage: integer
- interval_km: integer
- interval_days: integer
- last_service_date: date
- last_service_mileage: integer
- alert_km_before: integer (default: 1000)
- alert_days_before: integer (default: 7)
- is_active: boolean
- created_at, updated_at: timestamps
```

#### 4. `maintenance_operations` - Opérations
```sql
- id: bigint (PK)
- organization_id: bigint (FK, indexed)
- vehicle_id: bigint (FK, indexed)
- maintenance_type_id: bigint (FK)
- provider_id: bigint (FK, nullable)
- scheduled_date: datetime
- started_date: datetime (nullable)
- completed_date: datetime (nullable)
- cancelled_date: datetime (nullable)
- status: enum('planned', 'in_progress', 'completed', 'cancelled')
- priority: enum('low', 'medium', 'high', 'critical')
- description: text
- notes: text
- estimated_cost: decimal(12,2)
- total_cost: decimal(12,2)
- duration_minutes: integer
- mileage_at_service: integer
- created_by: bigint (FK users)
- cancelled_by: bigint (FK users, nullable)
- created_at, updated_at: timestamps
```

#### 5. `maintenance_alerts` - Alertes
```sql
- id: bigint (PK)
- organization_id: bigint (FK, indexed)
- vehicle_id: bigint (FK, indexed)
- schedule_id: bigint (FK, nullable)
- type: enum('maintenance_due', 'overdue', 'mileage_threshold', 'manual')
- priority: enum('low', 'medium', 'high', 'critical')
- message: text
- acknowledged_at: timestamp (nullable)
- acknowledged_by: bigint (FK users, nullable)
- acknowledgment_notes: text
- created_at, updated_at: timestamps
```

### Index et Optimisations
- Index composite sur `(organization_id, vehicle_id)`
- Index sur les dates de maintenance pour les requêtes temporelles
- Index sur les statuts pour les filtres fréquents
- Contraintes de clés étrangères avec CASCADE

## 🎯 Fonctionnalités Principales

### 1. Dashboard Enterprise
- **Métriques en temps réel:** Alertes, opérations, coûts
- **Graphiques interactifs:** Chart.js avec données live
- **Alertes critiques:** Affichage prioritaire
- **Prochaines maintenances:** Vue 7-14 jours
- **Opérations actives:** Suivi en cours

### 2. Gestion des Types de Maintenance
- **Catégories:** Préventive, Corrective, Inspection, Révision
- **Intervalles flexibles:** Basés sur temps ou kilométrage
- **Estimation coûts/durée:** Planification budgétaire
- **Configuration alertes:** Seuils personnalisables

### 3. Planification Intelligente
- **Auto-génération:** Basée sur intervalles définis
- **Alertes proactives:** Avant échéances
- **Vue calendrier:** Interface FullCalendar
- **Gestion des retards:** Suivi des dépassements

### 4. Opérations de Maintenance
- **Workflow complet:** Planifié → En cours → Terminé
- **Gestion fournisseurs:** Attribution et évaluation
- **Suivi coûts:** Estimé vs réel
- **Documentation:** Notes et pièces jointes

### 5. Système d'Alertes Automatiques
- **Jobs Laravel:** Vérification quotidienne automatique
- **Priorités graduées:** Low → Critical
- **Notifications multi-canal:** Email, SMS, in-app
- **Escalade:** Selon règles métier

### 6. Rapports et Analytiques
- **KPIs maintenance:** MTBF, MTTR, disponibilité
- **Analyse coûts:** Évolution, répartition, prévisions
- **Performance fournisseurs:** Évaluation comparative
- **Conformité réglementaire:** Suivi obligations

### 7. API REST Complète
- **Authentification Sanctum:** Sécurité enterprise
- **Pagination avancée:** Performance optimisée
- **Filtrage flexible:** Multi-critères
- **Resources Eloquent:** Données structurées
- **Health checks:** Monitoring intégré

## 🔧 Composants Livewire

### 1. ScheduleManager
```php
- Vue liste/calendrier
- Filtrage temps réel
- Actions en lot
- Export Excel
- Création d'alertes
```

### 2. OperationForm
```php
- Formulaire intelligent
- Validation temps réel
- Gestion documents
- Workflow statuts
- Calculs automatiques
```

### 3. AlertsDashboard
```php
- Notifications temps réel
- Acquittement en lot
- Création opérations
- Filtres avancés
- Actualisation auto
```

## 📡 API Endpoints

### Alertes
```
GET    /api/v1/maintenance/alerts
GET    /api/v1/maintenance/alerts/{id}
POST   /api/v1/maintenance/alerts/{id}/acknowledge
GET    /api/v1/maintenance/alerts/critical/latest
```

### Opérations
```
GET    /api/v1/maintenance/operations
POST   /api/v1/maintenance/operations
GET    /api/v1/maintenance/operations/{id}
PUT    /api/v1/maintenance/operations/{id}
GET    /api/v1/maintenance/operations/upcoming/list
```

### Planifications
```
GET    /api/v1/maintenance/schedules
POST   /api/v1/maintenance/schedules
```

### Dashboard & Stats
```
GET    /api/v1/maintenance/dashboard/stats
GET    /api/v1/maintenance/health
```

## 🔒 Sécurité et Permissions

### Multi-Tenant Security
- Isolation automatique par organisation
- Validation systématique des accès
- Global scopes sur tous les modèles
- Middleware de vérification

### Authentification API
- Sanctum tokens
- Rate limiting
- Webhook security
- Health check endpoints

## 📈 Performance et Monitoring

### Optimisations Base de Données
- Index stratégiques
- Requêtes optimisées
- Pagination efficace
- Eager loading automatique

### Health Checks
- Connectivité base de données
- Cache système
- Accès organisation
- Status module maintenance

### Monitoring
- Logs structurés
- Métriques de performance
- Alertes système
- Dashboard d'administration

## 🚀 Installation et Configuration

### 1. Migrations
```bash
php artisan migrate
```

### 2. Seeder
```bash
php artisan db:seed --class=MaintenanceModuleSeeder
```

### 3. Configuration Queue
```bash
php artisan queue:work
```

### 4. Cron Jobs
```bash
# Ajouter au crontab
* * * * * cd /path/to/zenfleet && php artisan schedule:run >> /dev/null 2>&1
```

## 📋 Utilisation

### Accès Web
- **Dashboard:** `/admin/maintenance`
- **Rapports:** `/admin/maintenance/reports`
- **API Docs:** `/api/docs`

### API Testing
```bash
# Health check
curl -X GET "http://localhost/api/health"

# Authentifié (avec token Sanctum)
curl -X GET "http://localhost/api/v1/maintenance/alerts" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🔄 Jobs et Automatisation

### CheckMaintenanceSchedulesJob
- **Fréquence:** Quotidienne
- **Fonction:** Vérification des échéances
- **Actions:** Création d'alertes automatiques

### SendMaintenanceAlertJob
- **Trigger:** Nouvelle alerte critique
- **Fonction:** Notifications multi-canal
- **Escalade:** Selon configuration

## 📊 Rapports Disponibles

### 1. Rapport de Performance
- Efficacité par type
- Temps de résolution
- Taux de réussite
- Tendances temporelles

### 2. Analyse des Coûts
- Évolution mensuelle
- Répartition par catégorie
- Coût par véhicule
- Prévisions budgétaires

### 3. KPIs Maintenance
- MTBF (Mean Time Between Failures)
- MTTR (Mean Time To Repair)
- Taux de disponibilité
- Score de conformité

### 4. Conformité Réglementaire
- Contrôles techniques
- Maintenance obligatoire
- Alertes de conformité
- Score global

### 5. Analyse Fournisseurs
- Performance comparative
- Analyse des coûts
- Délais d'intervention
- Recommandations

## 🛠️ Extensibilité

### Webhooks
- Alertes critiques externes
- Mise à jour kilométrage automatique
- Intégrations tierces
- Notifications personnalisées

### Personnalisation
- Types de maintenance custom
- Workflows adaptables
- Rapports personnalisés
- Seuils configurables

## 💡 Bonnes Pratiques

### Utilisation Optimale
1. **Configuration initiale complète** des types de maintenance
2. **Formation utilisateurs** sur les workflows
3. **Vérification régulière** des planifications
4. **Exploitation des rapports** pour l'optimisation
5. **Maintenance préventive privilégiée**

### Monitoring Recommandé
- Suivi quotidien des alertes critiques
- Analyse mensuelle des coûts
- Review trimestrielle des KPIs
- Audit annuel de conformité

## 🆘 Support et Maintenance

### Logs Système
- Application: `storage/logs/laravel.log`
- Jobs: `storage/logs/queue.log`
- API: Headers de debug activés

### Débogage
- Test script: `php test_maintenance_module.php`
- Health check: `/api/v1/maintenance/health`
- Artisan commands: `php artisan maintenance:*`

---

## 🎉 Conclusion

Le module Maintenance Enterprise-Grade de ZenFleet représente une solution complète et professionnelle pour la gestion avancée de maintenance de flotte. Avec son architecture robuste, ses fonctionnalités étendues et sa conception enterprise, il répond aux besoins les plus exigeants des gestionnaires de flotte modernes.

**Développé avec ❤️ par Claude Code - Expert Laravel Enterprise**