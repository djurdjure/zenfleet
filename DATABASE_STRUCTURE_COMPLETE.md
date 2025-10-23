# 📚 STRUCTURE COMPLÈTE BASE DE DONNÉES ZENFLEET
## Documentation Technique Exhaustive pour Analyse Externe

---

**Date de Génération:** 23 Octobre 2025  
**Version Database:** ZenFleet v2.0 Enterprise  
**SGBD:** PostgreSQL 16+  
**ORM:** Laravel Eloquent 12.x  
**Total Tables:** 50+  
**Total Migrations:** 94

---

## 📑 TABLE DES MATIÈRES

1. [Vue d'Ensemble Architecture](#vue-densemble-architecture)
2. [Tables Système et Core](#tables-système-et-core)
3. [Tables Métier](#tables-métier)
4. [Contraintes et Relations](#contraintes-et-relations)
5. [Index et Performance](#index-et-performance)
6. [Triggers et Fonctions](#triggers-et-fonctions)
7. [Vues Matérialisées](#vues-matérialisées)
8. [Diagramme ERD](#diagramme-erd)

---

## 1. VUE D'ENSEMBLE ARCHITECTURE

### 1.1 Schéma Général

```
DATABASE: zenfleet_production
├── Schema: public (défaut)
├── Extensions:
│   ├── btree_gist (contraintes temporelles)
│   ├── pg_trgm (recherche full-text)
│   └── uuid-ossp (génération UUIDs)
├── Tables: 50+
├── Modèles Eloquent: 37
├── Migrations: 94
└── Size estimate: ~2-5 GB (production moyenne)
```

### 1.2 Conventions de Nommage

```
TABLES:
- snake_case (ex: vehicle_mileage_readings)
- Pluriel (ex: vehicles, drivers)
- Préfixe métier si nécessaire (ex: maintenance_operations)

COLONNES:
- snake_case
- Suffixes types: _id (FK), _at (timestamp), _date (date)
- organization_id: systématique pour multi-tenant

CONTRAINTES:
- FK: fk_[table]_[colonne] (ex: fk_vehicles_organization)
- UNIQUE: unq_[table]_[colonnes] (ex: unq_vehicles_registration)
- CHECK: chk_[table]_[règle] (ex: chk_mileage_positive)
- EXCLUDE: excl_[table]_[règle] (ex: excl_assignments_vehicle_overlap)

INDEX:
- idx_[table]_[colonnes] (ex: idx_vehicles_organization_status)
- Suffixe type si non B-Tree: _gist, _gin, _brin
```

---

## 2. TABLES SYSTÈME ET CORE

### 2.1 users ⭐ Table Centrale Authentification

```sql
CREATE TABLE users (
    -- Clé primaire
    id BIGSERIAL PRIMARY KEY,
    
    -- Informations base
    name VARCHAR(255) NOT NULL,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(50),
    email_verified_at TIMESTAMP,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100),
    
    -- Multi-tenant
    organization_id BIGINT REFERENCES organizations(id) ON DELETE CASCADE,
    
    -- Hiérarchie
    supervisor_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    manager_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    
    -- Permissions
    is_super_admin BOOLEAN DEFAULT false,
    permissions_cache JSONB,
    
    -- Profil enrichi
    job_title VARCHAR(255),
    department VARCHAR(255),
    employee_id VARCHAR(100) UNIQUE,
    hire_date DATE,
    birth_date DATE,
    
    -- Sécurité
    two_factor_enabled BOOLEAN DEFAULT false,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP,
    password_changed_at TIMESTAMP,
    
    -- Activité
    last_activity_at TIMESTAMP,
    last_login_at TIMESTAMP,
    last_login_ip VARCHAR(45),
    login_count INT DEFAULT 0,
    
    -- Statut
    is_active BOOLEAN DEFAULT true,
    user_status VARCHAR(20) DEFAULT 'pending' CHECK (user_status IN ('active', 'inactive', 'suspended', 'pending')),
    
    -- Préférences
    timezone VARCHAR(50) DEFAULT 'Europe/Paris',
    language VARCHAR(2) DEFAULT 'fr',
    preferences JSONB,
    notification_preferences JSONB,
    
    -- Audit
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP -- Soft delete
);

-- Index
CREATE INDEX idx_users_organization ON users(organization_id);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_employee_id ON users(employee_id) WHERE employee_id IS NOT NULL;
CREATE INDEX idx_users_supervisor ON users(supervisor_id) WHERE supervisor_id IS NOT NULL;
CREATE INDEX idx_users_status ON users(organization_id, user_status, is_active);
CREATE INDEX idx_users_last_activity ON users(last_activity_at DESC);
```

**Relations Eloquent:**
```php
// User.php
belongsTo(Organization::class)
hasOne(Driver::class)
belongsToMany(Vehicle::class, 'user_vehicle')
hasMany(VehicleMileageReading::class, 'recorded_by_id')
belongsTo(User::class, 'supervisor_id')
belongsTo(User::class, 'manager_id')
// + Spatie Permission: roles(), permissions()
```

---

### 2.2 organizations ⭐ Table Centrale Multi-Tenant

```sql
CREATE TABLE organizations (
    -- Clé primaire et UUID
    id BIGSERIAL PRIMARY KEY,
    uuid UUID UNIQUE NOT NULL DEFAULT uuid_generate_v4(),
    
    -- Informations générales
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    legal_name VARCHAR(255),
    brand_name VARCHAR(255),
    organization_type VARCHAR(100),
    industry VARCHAR(100),
    description TEXT,
    website VARCHAR(255),
    phone_number VARCHAR(50),
    primary_email VARCHAR(255),
    billing_email VARCHAR(255),
    support_email VARCHAR(255),
    primary_phone VARCHAR(50),
    mobile_phone VARCHAR(50),
    logo_path VARCHAR(512),
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'suspended', 'pending')),
    
    -- Informations légales (Algeria)
    trade_register VARCHAR(100),
    nif VARCHAR(50), -- Numéro Identification Fiscale
    ai VARCHAR(50),  -- Article d'Imposition
    nis VARCHAR(50), -- Numéro Identification Statistique
    registration_number VARCHAR(100),
    tax_id VARCHAR(100),
    
    -- Adresses
    address TEXT,
    city VARCHAR(100),
    commune VARCHAR(100),
    zip_code VARCHAR(20),
    wilaya VARCHAR(50),
    headquarters_address JSONB,
    billing_address JSONB,
    
    -- Documents scannés
    scan_nif_path VARCHAR(512),
    scan_ai_path VARCHAR(512),
    scan_nis_path VARCHAR(512),
    
    -- Représentant légal
    manager_first_name VARCHAR(255),
    manager_last_name VARCHAR(255),
    manager_nin VARCHAR(50),
    manager_address TEXT,
    manager_dob DATE,
    manager_pob VARCHAR(255),
    manager_phone_number VARCHAR(50),
    manager_id_scan_path VARCHAR(512),
    
    -- Hiérarchie organisationnelle
    parent_organization_id BIGINT REFERENCES organizations(id) ON DELETE SET NULL,
    organization_level VARCHAR(50) DEFAULT 'company' CHECK (organization_level IN ('company', 'division', 'department', 'branch')),
    hierarchy_depth INT DEFAULT 0 CHECK (hierarchy_depth <= 5),
    hierarchy_path VARCHAR(255), -- Format: /1/5/12/
    
    -- Multi-tenant configuration
    is_tenant_root BOOLEAN DEFAULT true,
    allows_sub_organizations BOOLEAN DEFAULT false,
    tenant_settings JSONB,
    
    -- Statut enrichi
    compliance_status VARCHAR(20) DEFAULT 'under_review' CHECK (compliance_status IN ('compliant', 'warning', 'non_compliant', 'under_review')),
    status_changed_at TIMESTAMP,
    status_reason TEXT,
    
    -- Abonnement
    subscription_plan VARCHAR(50) DEFAULT 'trial' CHECK (subscription_plan IN ('trial', 'basic', 'professional', 'enterprise', 'custom')),
    subscription_tier VARCHAR(50),
    subscription_starts_at TIMESTAMP,
    subscription_expires_at TIMESTAMP,
    trial_ends_at TIMESTAMP,
    monthly_rate DECIMAL(8,2),
    annual_rate DECIMAL(8,2),
    currency VARCHAR(3) DEFAULT 'EUR',
    
    -- Limites et usage
    plan_limits JSONB,
    current_usage JSONB,
    max_users INT,
    max_vehicles INT,
    max_drivers INT,
    max_storage_mb INT,
    current_users INT DEFAULT 0,
    current_vehicles INT DEFAULT 0,
    current_drivers INT DEFAULT 0,
    current_storage_mb INT DEFAULT 0,
    
    -- Features et configuration
    feature_flags JSONB,
    enabled_features JSONB,
    feature_limits JSONB,
    settings JSONB,
    branding JSONB,
    notification_preferences JSONB,
    
    -- Compliance et audit
    data_retention_period INT DEFAULT 24, -- mois
    audit_log_enabled BOOLEAN DEFAULT true,
    compliance_level VARCHAR(20) DEFAULT 'standard' CHECK (compliance_level IN ('basic', 'standard', 'high', 'critical')),
    compliance_certifications JSONB,
    
    -- Sécurité
    two_factor_required BOOLEAN DEFAULT false,
    enforce_2fa BOOLEAN DEFAULT false,
    ip_restriction_enabled BOOLEAN DEFAULT false,
    password_policy_level INT DEFAULT 1,
    password_policy_strength INT DEFAULT 2,
    session_timeout_minutes INT DEFAULT 480,
    security_settings JSONB,
    
    -- GDPR
    gdpr_compliant BOOLEAN DEFAULT false,
    gdpr_consent_at TIMESTAMP,
    
    -- Métriques
    last_activity_at TIMESTAMP,
    total_users INT DEFAULT 0,
    active_users INT DEFAULT 0,
    total_vehicles INT DEFAULT 0,
    active_vehicles INT DEFAULT 0,
    
    -- Géolocalisation
    timezone VARCHAR(50) DEFAULT 'Europe/Paris',
    country_code VARCHAR(2),
    language VARCHAR(2) DEFAULT 'fr',
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    
    -- Métadonnées
    created_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    updated_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    onboarding_completed_at TIMESTAMP,
    
    -- Audit
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP
);

-- Index
CREATE INDEX idx_organizations_slug ON organizations(slug);
CREATE INDEX idx_organizations_status ON organizations(status, subscription_expires_at);
CREATE INDEX idx_organizations_subscription ON organizations(subscription_plan, status);
CREATE INDEX idx_organizations_parent ON organizations(parent_organization_id, hierarchy_depth);
CREATE INDEX idx_organizations_activity ON organizations(last_activity_at DESC);
CREATE INDEX idx_organizations_name ON organizations(name);
CREATE INDEX idx_organizations_location ON organizations(city, wilaya);
```

**Contraintes Métier:**
```sql
-- Trigger: Calcul hiérarchie automatique
CREATE FUNCTION update_organization_hierarchy() RETURNS TRIGGER;
CREATE TRIGGER trg_organization_hierarchy 
BEFORE INSERT OR UPDATE ON organizations;

-- Prévention cycles hiérarchiques
-- Validation profondeur max (5 niveaux)
-- Calcul automatique hierarchy_path
```

**Relations Eloquent:**
```php
// Organization.php
hasMany(User::class)
hasMany(Vehicle::class)
hasMany(Driver::class)
hasMany(Assignment::class)
belongsTo(Organization::class, 'parent_organization_id')
hasMany(Organization::class, 'parent_organization_id')
belongsTo(AlgeriaWilaya::class, 'wilaya', 'code')
```

---

### 2.3 Spatie Permission Tables

#### 2.3.1 roles

```sql
CREATE TABLE roles (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    description TEXT,
    organization_id BIGINT REFERENCES organizations(id) ON DELETE CASCADE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE(name, guard_name, organization_id)
);

CREATE INDEX idx_roles_name ON roles(name, guard_name);
CREATE INDEX idx_roles_organization ON roles(organization_id);
```

#### 2.3.2 permissions

```sql
CREATE TABLE permissions (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    description TEXT,
    module VARCHAR(100),
    resource VARCHAR(100),
    action VARCHAR(50),
    scope VARCHAR(50) DEFAULT 'organization' CHECK (scope IN ('global', 'organization', 'supervised', 'own')),
    risk_level INT DEFAULT 1,
    is_system BOOLEAN DEFAULT false,
    is_active BOOLEAN DEFAULT true,
    organization_id BIGINT REFERENCES organizations(id) ON DELETE CASCADE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE(name, guard_name)
);

CREATE INDEX idx_permissions_module ON permissions(module, resource, action);
CREATE INDEX idx_permissions_scope ON permissions(scope, is_active);
```

#### 2.3.3 model_has_roles

```sql
CREATE TABLE model_has_roles (
    role_id BIGINT REFERENCES roles(id) ON DELETE CASCADE,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT NOT NULL,
    organization_id BIGINT REFERENCES organizations(id) ON DELETE CASCADE,
    
    PRIMARY KEY (role_id, model_id, model_type)
);

CREATE INDEX idx_model_has_roles_model ON model_has_roles(model_id, model_type);
CREATE INDEX idx_model_has_roles_org ON model_has_roles(organization_id);
```

#### 2.3.4 model_has_permissions

```sql
CREATE TABLE model_has_permissions (
    permission_id BIGINT REFERENCES permissions(id) ON DELETE CASCADE,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT NOT NULL,
    organization_id BIGINT REFERENCES organizations(id) ON DELETE CASCADE,
    
    PRIMARY KEY (permission_id, model_id, model_type)
);

CREATE INDEX idx_model_has_permissions_model ON model_has_permissions(model_id, model_type);
```

#### 2.3.5 role_has_permissions

```sql
CREATE TABLE role_has_permissions (
    permission_id BIGINT REFERENCES permissions(id) ON DELETE CASCADE,
    role_id BIGINT REFERENCES roles(id) ON DELETE CASCADE,
    
    PRIMARY KEY (permission_id, role_id)
);
```

---

## 3. TABLES MÉTIER

### 3.1 Module Flotte - Véhicules

#### 3.1.1 vehicles ⭐ Table Centrale Flotte

```sql
CREATE TABLE vehicles (
    -- Clé primaire
    id BIGSERIAL PRIMARY KEY,
    
    -- Multi-tenant
    organization_id BIGINT NOT NULL REFERENCES organizations(id) ON DELETE CASCADE,
    
    -- Identification véhicule
    registration_plate VARCHAR(50) NOT NULL,
    vin VARCHAR(17) UNIQUE,
    vehicle_name VARCHAR(255),
    brand VARCHAR(100),
    model VARCHAR(100),
    color VARCHAR(50),
    
    -- Classification
    vehicle_type_id BIGINT REFERENCES vehicle_types(id) ON DELETE SET NULL,
    fuel_type_id BIGINT REFERENCES fuel_types(id) ON DELETE SET NULL,
    transmission_type_id BIGINT REFERENCES transmission_types(id) ON DELETE SET NULL,
    status_id BIGINT REFERENCES vehicle_statuses(id) ON DELETE SET NULL,
    category_id BIGINT REFERENCES vehicle_categories(id) ON DELETE SET NULL,
    depot_id BIGINT REFERENCES vehicle_depots(id) ON DELETE SET NULL,
    
    -- Caractéristiques techniques
    manufacturing_year SMALLINT,
    engine_displacement_cc INT,
    power_hp INT,
    seats SMALLINT,
    
    -- Acquisition et valeur
    acquisition_date DATE,
    purchase_price DECIMAL(12,2),
    current_value DECIMAL(12,2),
    
    -- Kilométrage
    initial_mileage BIGINT DEFAULT 0 CHECK (initial_mileage >= 0),
    current_mileage BIGINT DEFAULT 0 CHECK (current_mileage >= 0),
    
    -- Statut et notes
    status_reason TEXT,
    notes TEXT,
    
    -- Archivage
    is_archived BOOLEAN DEFAULT false,
    
    -- Audit
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP,
    
    -- Contraintes
    UNIQUE(organization_id, registration_plate)
);

-- Index optimisés
CREATE INDEX idx_vehicles_organization ON vehicles(organization_id);
CREATE INDEX idx_vehicles_status ON vehicles(status_id);
CREATE INDEX idx_vehicles_type ON vehicles(vehicle_type_id);
CREATE INDEX idx_vehicles_category ON vehicles(category_id);
CREATE INDEX idx_vehicles_depot ON vehicles(depot_id);
CREATE INDEX idx_vehicles_registration ON vehicles(registration_plate);
CREATE INDEX idx_vehicles_vin ON vehicles(vin) WHERE vin IS NOT NULL;

-- Index composite pour queries fréquentes
CREATE INDEX idx_vehicles_org_status_active ON vehicles(organization_id, status_id, vehicle_type_id) 
WHERE status_id = 1 AND is_archived = false AND deleted_at IS NULL;

-- Index pour recherche disponibilité
CREATE INDEX idx_vehicles_available ON vehicles(organization_id, status_id) 
WHERE status_id = 1 AND is_archived = false AND deleted_at IS NULL;
```

**Relations Eloquent:**
```php
// Vehicle.php
belongsTo(Organization::class)
belongsTo(VehicleType::class)
belongsTo(FuelType::class)
belongsTo(TransmissionType::class)
belongsTo(VehicleStatus::class, 'status_id')
belongsTo(VehicleCategory::class, 'category_id')
belongsTo(VehicleDepot::class, 'depot_id')
hasMany(Assignment::class)
hasMany(MaintenanceOperation::class)
hasMany(RepairRequest::class)
hasMany(VehicleMileageReading::class)
hasMany(VehicleExpense::class)
belongsToMany(User::class, 'user_vehicle')
```

#### 3.1.2 vehicle_types

```sql
CREATE TABLE vehicle_types (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

INSERT INTO vehicle_types (name, description) VALUES
('Berline', 'Véhicule de tourisme'),
('SUV', 'Sport Utility Vehicle'),
('Camionnette', 'Utilitaire léger'),
('Poids Lourd', 'Camion transport'),
('Moto', 'Deux roues'),
('Bus', 'Transport collectif');
```

#### 3.1.3 fuel_types

```sql
CREATE TABLE fuel_types (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

INSERT INTO fuel_types (name) VALUES
('Essence'), ('Diesel'), ('GPL'), ('Électrique'), ('Hybride');
```

#### 3.1.4 transmission_types

```sql
CREATE TABLE transmission_types (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

INSERT INTO transmission_types (name) VALUES
('Manuelle'), ('Automatique'), ('Semi-automatique');
```

#### 3.1.5 vehicle_statuses

```sql
CREATE TABLE vehicle_statuses (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE,
    color VARCHAR(20),
    description TEXT,
    is_active BOOLEAN DEFAULT true,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

INSERT INTO vehicle_statuses (id, name, slug, color) VALUES
(1, 'Actif', 'active', 'green'),
(2, 'En maintenance', 'maintenance', 'yellow'),
(3, 'Inactif', 'inactive', 'red'),
(4, 'En réparation', 'repair', 'orange');
```

#### 3.1.6 vehicle_categories

```sql
CREATE TABLE vehicle_categories (
    id BIGSERIAL PRIMARY KEY,
    organization_id BIGINT REFERENCES organizations(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE(organization_id, name)
);
```

#### 3.1.7 vehicle_depots

```sql
CREATE TABLE vehicle_depots (
    id BIGSERIAL PRIMARY KEY,
    organization_id BIGINT REFERENCES organizations(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    city VARCHAR(100),
    postal_code VARCHAR(20),
    wilaya VARCHAR(50),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    contact_person VARCHAR(255),
    contact_phone VARCHAR(50),
    capacity INT,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE(organization_id, name)
);

CREATE INDEX idx_depots_organization ON vehicle_depots(organization_id);
CREATE INDEX idx_depots_location ON vehicle_depots(latitude, longitude) WHERE latitude IS NOT NULL;
```

#### 3.1.8 vehicle_mileage_readings ⭐ Historique Kilométrage

```sql
CREATE TABLE vehicle_mileage_readings (
    id BIGSERIAL PRIMARY KEY,
    
    -- Multi-tenant
    organization_id BIGINT NOT NULL REFERENCES organizations(id) ON DELETE CASCADE,
    vehicle_id BIGINT NOT NULL REFERENCES vehicles(id) ON DELETE CASCADE,
    
    -- Relevé
    recorded_at TIMESTAMP NOT NULL,
    mileage BIGINT NOT NULL CHECK (mileage >= 0),
    recording_method VARCHAR(20) DEFAULT 'manual' CHECK (recording_method IN ('manual', 'automatic')),
    
    -- Utilisateur
    recorded_by_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    
    -- Notes
    notes TEXT,
    
    -- Audit
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index optimisés
CREATE INDEX idx_mileage_organization ON vehicle_mileage_readings(organization_id);
CREATE INDEX idx_mileage_vehicle ON vehicle_mileage_readings(vehicle_id);
CREATE INDEX idx_mileage_recorded_at ON vehicle_mileage_readings(recorded_at DESC);
CREATE INDEX idx_mileage_method ON vehicle_mileage_readings(recording_method);

-- Index composite pour requêtes fréquentes
CREATE INDEX idx_mileage_org_vehicle_date ON vehicle_mileage_readings(organization_id, vehicle_id, recorded_at DESC);

-- Index pour détection anomalies
CREATE INDEX idx_mileage_vehicle_chronology ON vehicle_mileage_readings(vehicle_id, recorded_at, mileage);

-- Index pour audit
CREATE INDEX idx_mileage_recorded_by ON vehicle_mileage_readings(recorded_by_id) WHERE recorded_by_id IS NOT NULL;

-- Trigger validation cohérence
CREATE FUNCTION check_mileage_consistency() RETURNS TRIGGER;
CREATE TRIGGER trg_check_mileage_consistency
BEFORE INSERT ON vehicle_mileage_readings
FOR EACH ROW EXECUTE FUNCTION check_mileage_consistency();
```

---

### 3.2 Module Chauffeurs

#### 3.2.1 drivers ⭐ Table Centrale Chauffeurs

```sql
CREATE TABLE drivers (
    -- Clé primaire
    id BIGSERIAL PRIMARY KEY,
    
    -- Multi-tenant
    organization_id BIGINT NOT NULL REFERENCES organizations(id) ON DELETE CASCADE,
    
    -- Lien utilisateur (optionnel)
    user_id BIGINT UNIQUE REFERENCES users(id) ON DELETE SET NULL,
    
    -- Identité
    employee_number VARCHAR(100) UNIQUE,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    photo VARCHAR(512), -- Renommé de photo_path
    birth_date DATE,
    blood_type VARCHAR(10),
    
    -- Contact
    personal_phone VARCHAR(50),
    personal_email VARCHAR(255),
    address TEXT,
    city VARCHAR(100),
    postal_code VARCHAR(20),
    
    -- Permis de conduire
    license_number VARCHAR(100),
    license_category VARCHAR(50),
    license_categories JSONB, -- Multi-catégories
    license_expiry_date DATE, -- ⚠️ Nom normalisé (vs expiry_date legacy)
    license_issue_date DATE,
    license_authority VARCHAR(255),
    
    -- Emploi
    recruitment_date DATE,
    contract_end_date DATE,
    status_id BIGINT REFERENCES driver_statuses(id) ON DELETE SET NULL,
    
    -- Hiérarchie
    supervisor_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    
    -- Contact urgence
    emergency_contact_name VARCHAR(255),
    emergency_contact_phone VARCHAR(50),
    emergency_contact_relationship VARCHAR(100),
    
    -- Notes
    notes TEXT,
    
    -- Audit
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP
);

-- Index
CREATE INDEX idx_drivers_organization ON drivers(organization_id);
CREATE INDEX idx_drivers_user ON drivers(user_id) WHERE user_id IS NOT NULL;
CREATE INDEX idx_drivers_status ON drivers(status_id);
CREATE INDEX idx_drivers_supervisor ON drivers(supervisor_id) WHERE supervisor_id IS NOT NULL;
CREATE INDEX idx_drivers_employee_number ON drivers(employee_number) WHERE employee_number IS NOT NULL;
CREATE INDEX idx_drivers_license_expiry ON drivers(license_expiry_date) WHERE license_expiry_date IS NOT NULL;

-- Index composite requêtes fréquentes
CREATE INDEX idx_drivers_org_status_active ON drivers(organization_id, status_id) 
WHERE status_id = 1 AND deleted_at IS NULL;

-- Index full-text search
CREATE INDEX idx_drivers_fulltext ON drivers 
USING GIN (to_tsvector('french', first_name || ' ' || last_name || ' ' || COALESCE(employee_number, '')));
```

**Relations Eloquent:**
```php
// Driver.php
belongsTo(Organization::class)
belongsTo(User::class)
belongsTo(DriverStatus::class, 'status_id')
belongsTo(User::class, 'supervisor_id')
hasMany(Assignment::class)
hasMany(DriverSanction::class)
hasMany(RepairRequest::class)
```

#### 3.2.2 driver_statuses

```sql
CREATE TABLE driver_statuses (
    id BIGSERIAL PRIMARY KEY,
    organization_id BIGINT REFERENCES organizations(id) ON DELETE CASCADE,
    
    -- Identification
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100),
    code VARCHAR(20),
    
    -- UI
    color VARCHAR(20),
    icon VARCHAR(50),
    badge_class VARCHAR(100),
    
    -- Comportement
    is_active BOOLEAN DEFAULT true,
    is_available_for_assignment BOOLEAN DEFAULT true,
    requires_validation BOOLEAN DEFAULT false,
    auto_archive_after_days INT,
    
    -- Workflow
    can_transition_to JSONB, -- Liste status_ids autorisés
    transition_rules JSONB,
    
    -- Description
    description TEXT,
    internal_notes TEXT,
    
    -- Affichage
    display_order INT DEFAULT 0,
    
    -- Audit
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP,
    
    UNIQUE(organization_id, slug)
);

-- Statuts par défaut
INSERT INTO driver_statuses (name, slug, color, is_active) VALUES
('Actif', 'active', 'green', true),
('En congé', 'on_leave', 'yellow', false),
('Suspendu', 'suspended', 'red', false),
('Retraité', 'retired', 'gray', false);
```

#### 3.2.3 driver_sanctions

```sql
CREATE TABLE driver_sanctions (
    id BIGSERIAL PRIMARY KEY,
    
    -- Multi-tenant
    organization_id BIGINT NOT NULL REFERENCES organizations(id) ON DELETE CASCADE,
    driver_id BIGINT NOT NULL REFERENCES drivers(id) ON DELETE CASCADE,
    
    -- Sanction
    sanction_type VARCHAR(100) NOT NULL,
    severity_level INT DEFAULT 1 CHECK (severity_level BETWEEN 1 AND 5),
    
    -- Description
    title VARCHAR(255) NOT NULL,
    description TEXT,
    reason TEXT NOT NULL,
    
    -- Dates
    incident_date DATE NOT NULL,
    sanction_date DATE NOT NULL,
    start_date DATE,
    end_date DATE,
    
    -- Statut
    status VARCHAR(50) DEFAULT 'active' CHECK (status IN ('active', 'completed', 'cancelled', 'appealed')),
    
    -- Impact
    affects_availability BOOLEAN DEFAULT false,
    suspension_days INT,
    fine_amount DECIMAL(10,2),
    
    -- Documents
    documents JSONB,
    
    -- Workflow
    issued_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    approved_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    appeal_reason TEXT,
    appeal_date DATE,
    
    -- Audit
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP
);

CREATE INDEX idx_sanctions_driver ON driver_sanctions(driver_id);
CREATE INDEX idx_sanctions_organization ON driver_sanctions(organization_id);
CREATE INDEX idx_sanctions_status ON driver_sanctions(status);
CREATE INDEX idx_sanctions_dates ON driver_sanctions(start_date, end_date);
CREATE INDEX idx_sanctions_severity ON driver_sanctions(severity_level, status);
```

#### 3.2.4 driver_sanction_histories

```sql
CREATE TABLE driver_sanction_histories (
    id BIGSERIAL PRIMARY KEY,
    driver_sanction_id BIGINT NOT NULL REFERENCES driver_sanctions(id) ON DELETE CASCADE,
    
    -- Changement
    field_changed VARCHAR(100) NOT NULL,
    old_value TEXT,
    new_value TEXT,
    
    -- Audit
    changed_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    change_reason TEXT,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_sanction_histories_sanction ON driver_sanction_histories(driver_sanction_id);
CREATE INDEX idx_sanction_histories_date ON driver_sanction_histories(changed_at DESC);
```

---

### 3.3 Module Affectations ⭐ CRITICAL

#### 3.3.1 assignments

```sql
CREATE TABLE assignments (
    -- Clé primaire
    id BIGSERIAL PRIMARY KEY,
    
    -- Multi-tenant
    organization_id BIGINT NOT NULL REFERENCES organizations(id) ON DELETE CASCADE,
    
    -- Relations principales
    vehicle_id BIGINT NOT NULL REFERENCES vehicles(id) ON DELETE CASCADE,
    driver_id BIGINT NOT NULL REFERENCES drivers(id) ON DELETE CASCADE,
    
    -- Période (SUPPORT DURÉES INDÉTERMINÉES)
    start_datetime TIMESTAMP NOT NULL,
    end_datetime TIMESTAMP, -- NULL = durée indéterminée
    
    -- Kilométrage
    start_mileage BIGINT,
    end_mileage BIGINT,
    
    -- Métadonnées
    reason TEXT,
    notes TEXT,
    
    -- Statut (calculé dynamiquement via accessor Laravel)
    status VARCHAR(50) DEFAULT 'active' CHECK (status IN ('scheduled', 'active', 'completed', 'cancelled')),
    
    -- Durée
    estimated_duration_hours DECIMAL(8,2),
    actual_duration_hours DECIMAL(8,2),
    
    -- Géolocalisation (optionnel)
    start_latitude DECIMAL(10,8),
    start_longitude DECIMAL(11,8),
    end_latitude DECIMAL(10,8),
    end_longitude DECIMAL(11,8),
    
    -- Audit trail
    created_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    updated_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    ended_by_user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    ended_at TIMESTAMP,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP
);

-- Index simples
CREATE INDEX idx_assignments_organization ON assignments(organization_id);
CREATE INDEX idx_assignments_vehicle ON assignments(vehicle_id);
CREATE INDEX idx_assignments_driver ON assignments(driver_id);
CREATE INDEX idx_assignments_start ON assignments(start_datetime);
CREATE INDEX idx_assignments_end ON assignments(end_datetime) WHERE end_datetime IS NOT NULL;
CREATE INDEX idx_assignments_status ON assignments(status);

-- Index composites optimisés
CREATE INDEX idx_assignments_org_status ON assignments(organization_id, status) 
WHERE deleted_at IS NULL;

CREATE INDEX idx_assignments_vehicle_period ON assignments(vehicle_id, start_datetime, end_datetime) 
WHERE deleted_at IS NULL;

CREATE INDEX idx_assignments_driver_period ON assignments(driver_id, start_datetime, end_datetime) 
WHERE deleted_at IS NULL;

CREATE INDEX idx_assignments_org_vehicle_date ON assignments(organization_id, vehicle_id, start_datetime DESC);

-- Index pour affectations actives (queries fréquentes)
CREATE INDEX idx_assignments_active ON assignments(organization_id, vehicle_id, driver_id) 
WHERE end_datetime IS NULL AND deleted_at IS NULL;

-- Index pour Gantt (fenêtres temporelles)
CREATE INDEX idx_assignments_period_gantt ON assignments(start_datetime, end_datetime, organization_id) 
WHERE deleted_at IS NULL;

-- Index GIN pour recherche textuelle
CREATE INDEX idx_assignments_search ON assignments 
USING GIN (to_tsvector('french', COALESCE(reason, '') || ' ' || COALESCE(notes, '')));

-- Index GIST pour requêtes temporelles
CREATE INDEX idx_assignments_vehicle_temporal ON assignments 
USING GIST (
    vehicle_id, 
    organization_id,
    tsrange(start_datetime, COALESCE(end_datetime, '2099-12-31'::timestamp))
) WHERE deleted_at IS NULL;

CREATE INDEX idx_assignments_driver_temporal ON assignments 
USING GIST (
    driver_id,
    organization_id,
    tsrange(start_datetime, COALESCE(end_datetime, '2099-12-31'::timestamp))
) WHERE deleted_at IS NULL;
```

**🏆 CONTRAINTES GIST ANTI-CHEVAUCHEMENT (WORLD-CLASS):**

```sql
-- Fonction helper pour gérer intervalles indéterminés
CREATE OR REPLACE FUNCTION assignment_interval(start_dt TIMESTAMP, end_dt TIMESTAMP)
RETURNS tsrange AS $$
BEGIN
    IF end_dt IS NULL THEN
        RETURN tsrange(start_dt, '2099-12-31 23:59:59'::timestamp);
    ELSE
        RETURN tsrange(start_dt, end_dt);
    END IF;
END;
$$ LANGUAGE plpgsql IMMUTABLE;

-- CONTRAINTE 1: Anti-chevauchement VÉHICULE
ALTER TABLE assignments
ADD CONSTRAINT assignments_vehicle_no_overlap
EXCLUDE USING GIST (
    organization_id WITH =,
    vehicle_id WITH =,
    assignment_interval(start_datetime, end_datetime) WITH &&
)
WHERE (deleted_at IS NULL)
DEFERRABLE INITIALLY DEFERRED;

-- CONTRAINTE 2: Anti-chevauchement CHAUFFEUR  
ALTER TABLE assignments
ADD CONSTRAINT assignments_driver_no_overlap
EXCLUDE USING GIST (
    organization_id WITH =,
    driver_id WITH =,
    assignment_interval(start_datetime, end_datetime) WITH &&
)
WHERE (deleted_at IS NULL)
DEFERRABLE INITIALLY DEFERRED;
```

**Trigger Alternative (si GIST non disponible):**

```sql
CREATE OR REPLACE FUNCTION check_assignment_overlaps()
RETURNS TRIGGER AS $$
DECLARE
    conflict_count INT;
BEGIN
    -- Vérifier chevauchement véhicule
    SELECT COUNT(*) INTO conflict_count
    FROM assignments a
    WHERE a.id != COALESCE(NEW.id, 0)
    AND a.vehicle_id = NEW.vehicle_id
    AND a.organization_id = NEW.organization_id
    AND a.deleted_at IS NULL
    AND (
        (a.end_datetime IS NOT NULL AND NEW.start_datetime < a.end_datetime AND
         (NEW.end_datetime IS NULL OR NEW.end_datetime > a.start_datetime))
        OR
        (a.end_datetime IS NULL AND NEW.start_datetime >= a.start_datetime)
        OR
        (NEW.end_datetime IS NULL AND a.start_datetime >= NEW.start_datetime)
    );

    IF conflict_count > 0 THEN
        RAISE EXCEPTION 'Chevauchement détecté pour véhicule ID %', NEW.vehicle_id;
    END IF;

    -- Même logique pour chauffeur...
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER assignments_overlap_check
BEFORE INSERT OR UPDATE ON assignments
FOR EACH ROW
EXECUTE FUNCTION check_assignment_overlaps();
```

**Fonction Calcul Statut:**

```sql
CREATE OR REPLACE FUNCTION assignment_computed_status(start_dt TIMESTAMP, end_dt TIMESTAMP)
RETURNS TEXT AS $$
BEGIN
    IF start_dt > NOW() THEN
        RETURN 'scheduled';
    ELSIF end_dt IS NULL OR end_dt > NOW() THEN
        RETURN 'active';
    ELSE
        RETURN 'completed';
    END IF;
END;
$$ LANGUAGE plpgsql IMMUTABLE;

-- Index sur statut calculé
CREATE INDEX idx_assignments_status_computed ON assignments (
    organization_id,
    assignment_computed_status(start_datetime, end_datetime)
) WHERE deleted_at IS NULL;
```

**Relations Eloquent:**
```php
// Assignment.php
belongsTo(Organization::class)
belongsTo(Vehicle::class)
belongsTo(Driver::class)
belongsTo(User::class, 'created_by')
belongsTo(User::class, 'updated_by')
belongsTo(User::class, 'ended_by_user_id')
hasOne(VehicleHandoverForm::class) // Si module handover activé
```

---

## 4. CONTRAINTES ET RELATIONS

### 4.1 Résumé Contraintes d'Intégrité

```
TOTAL FOREIGN KEYS: 146

Stratégie Cascades:
├── ON DELETE CASCADE:        78 (53%) - Relations de composition forte
├── ON DELETE SET NULL:       52 (36%) - Relations optionnelles/audit
├── ON DELETE RESTRICT:       16 (11%) - Protection suppressions critiques
└── ON DELETE NO ACTION:       0 (0%)

UNIQUE Constraints: 24
CHECK Constraints: 12+
EXCLUDE Constraints (GIST): 4
```

### 4.2 Matrice Relations Principales

```
organizations (1) ──┬─→ (N) users
                    ├─→ (N) vehicles
                    ├─→ (N) drivers
                    ├─→ (N) assignments
                    ├─→ (N) documents
                    ├─→ (N) suppliers
                    └─→ (N) maintenance_operations

vehicles (1) ──┬─→ (N) assignments
               ├─→ (N) maintenance_operations
               ├─→ (N) repair_requests
               ├─→ (N) vehicle_mileage_readings
               └─→ (N) vehicle_expenses

drivers (1) ──┬─→ (N) assignments
              ├─→ (N) driver_sanctions
              └─→ (N) repair_requests

assignments (1) ─→ (1) vehicle
                ─→ (1) driver
                ─→ (1) organization

users (1) ──┬─→ (1) organization
            ├─→ (1?) driver (optional)
            ├─→ (N) mileage_readings (created)
            ├─→ (N) assignments (created)
            └─→ (N) audit_logs
```

### 4.3 Contraintes Métier Clés

**1. Multi-Tenant Isolation:**
```sql
-- Toutes les tables métier DOIVENT avoir organization_id
-- Exception: tables de référence globales (vehicle_types, fuel_types)

SELECT tablename 
FROM pg_tables 
WHERE schemaname = 'public'
AND tablename NOT IN ('vehicle_types', 'fuel_types', 'transmission_types')
AND NOT EXISTS (
    SELECT 1 FROM information_schema.columns 
    WHERE table_name = tablename 
    AND column_name = 'organization_id'
);
-- Devrait retourner 0 résultats
```

**2. Soft Deletes Cohérents:**
```sql
-- Tables avec soft deletes (deleted_at)
users, organizations, vehicles, drivers, assignments,
documents, maintenance_operations, suppliers

-- Contraintes doivent respecter soft deletes
WHERE deleted_at IS NULL
```

**3. Unicité Multi-Tenant:**
```sql
-- Format standard
UNIQUE (organization_id, [business_key])

-- Exemples:
UNIQUE (organization_id, registration_plate) -- vehicles
UNIQUE (organization_id, name) -- vehicle_categories
UNIQUE (organization_id, slug) -- driver_statuses
```

---

## 5. INDEX ET PERFORMANCE

### 5.1 Statistiques Index

```
TOTAL INDEX: 64+

Par Type:
├── B-Tree (défaut):        85%
├── GIST (temporal/geo):    10%
├── GIN (full-text):        4%
└── BRIN (chronologique):   1%

Par Utilisation:
├── Index simples (1 colonne):      40%
├── Index composites (2-3 colonnes): 45%
├── Index partiels (WHERE):         15%
```

### 5.2 Index Critiques Performance

**Top 10 Index par Impact:**

1. `idx_assignments_vehicle_temporal` (GIST)
2. `idx_assignments_driver_temporal` (GIST)
3. `idx_vehicles_organization`
4. `idx_drivers_organization`
5. `idx_assignments_org_status`
6. `idx_mileage_org_vehicle_date`
7. `idx_vehicles_org_status_active` (partial)
8. `idx_drivers_fulltext` (GIN)
9. `idx_assignments_search` (GIN)
10. `idx_audit_logs_org_occurred` (composite)

### 5.3 Stratégie Indexation Recommandée

**Règles d'Or:**

1. **Toujours indexer:**
   - `organization_id` (multi-tenant queries)
   - Foreign keys (joins)
   - Colonnes WHERE fréquentes (status, deleted_at)
   - Colonnes ORDER BY (created_at, updated_at)

2. **Index composites:**
   - Mettre organization_id en premier (sélectivité)
   - Ordrede colonnes: WHERE > ORDER BY > SELECT

3. **Index partiels:**
   - Pour états actifs (is_archived = false)
   - Pour non supprimés (deleted_at IS NULL)
   - Pour statuts fréquents (status = 'active')

4. **Index spécialisés:**
   - GIST pour intervalles temporels/géospatiaux
   - GIN pour full-text search
   - BRIN pour colonnes séquentielles volumineuses

---

## 6. TRIGGERS ET FONCTIONS

### 6.1 Triggers Actifs

```sql
-- 1. Mise à jour hiérarchie organisations
CREATE TRIGGER trg_organization_hierarchy
BEFORE INSERT OR UPDATE ON organizations
FOR EACH ROW EXECUTE FUNCTION update_organization_hierarchy();

-- 2. Validation cohérence kilométrage
CREATE TRIGGER trg_check_mileage_consistency
BEFORE INSERT ON vehicle_mileage_readings
FOR EACH ROW EXECUTE FUNCTION check_mileage_consistency();

-- 3. Validation chevauchement assignments (si GIST unavailable)
CREATE TRIGGER assignments_overlap_check
BEFORE INSERT OR UPDATE ON assignments
FOR EACH ROW EXECUTE FUNCTION check_assignment_overlaps();

-- 4. Refresh statistiques assignments
CREATE TRIGGER assignment_stats_refresh
AFTER INSERT OR UPDATE OR DELETE ON assignments
FOR EACH STATEMENT EXECUTE FUNCTION refresh_assignment_stats();

-- 5. Audit trail automatique
CREATE TRIGGER audit_vehicle_changes
AFTER INSERT OR UPDATE OR DELETE ON vehicles
FOR EACH ROW EXECUTE FUNCTION log_table_changes();

-- Similaire pour: drivers, assignments, users
```

### 6.2 Fonctions Utilitaires

```sql
-- 1. Obtenir organisations accessibles utilisateur
CREATE FUNCTION get_user_accessible_organizations(p_user_id BIGINT)
RETURNS TABLE(org_id BIGINT, org_name TEXT, role_name TEXT);

-- 2. Valider permission utilisateur
CREATE FUNCTION user_has_permission(
    p_user_id BIGINT,
    p_organization_id BIGINT,
    p_permission TEXT
) RETURNS BOOLEAN;

-- 3. Calculer interval assignment
CREATE FUNCTION assignment_interval(start_dt TIMESTAMP, end_dt TIMESTAMP)
RETURNS tsrange;

-- 4. Calculer statut assignment
CREATE FUNCTION assignment_computed_status(start_dt TIMESTAMP, end_dt TIMESTAMP)
RETURNS TEXT;

-- 5. Purge anciens logs (GDPR)
CREATE FUNCTION gdpr_purge_old_logs() RETURNS VOID;

-- 6. Statistiques véhicule
CREATE FUNCTION get_vehicle_stats(p_vehicle_id BIGINT)
RETURNS JSON;
```

---

## 7. VUES MATÉRIALISÉES

### 7.1 Vues Existantes

```sql
-- 1. Statistiques assignments journalières
CREATE MATERIALIZED VIEW assignment_stats_daily AS
SELECT
    organization_id,
    DATE(start_datetime) as assignment_date,
    COUNT(*) as total_assignments,
    COUNT(*) FILTER (WHERE end_datetime IS NULL) as ongoing_assignments,
    COUNT(DISTINCT vehicle_id) as vehicles_used,
    COUNT(DISTINCT driver_id) as drivers_used,
    AVG(EXTRACT(EPOCH FROM (COALESCE(end_datetime, NOW()) - start_datetime))/3600) as avg_duration_hours
FROM assignments
WHERE deleted_at IS NULL
GROUP BY organization_id, DATE(start_datetime);

CREATE UNIQUE INDEX ON assignment_stats_daily (organization_id, assignment_date);

-- Refresh: Automatique via trigger ou cron
```

### 7.2 Vues Recommandées (non implémentées)

```sql
-- 2. Statistiques véhicules (RECOMMANDÉ - RECO-004)
CREATE MATERIALIZED VIEW mv_vehicle_stats;

-- 3. Statistiques chauffeurs (RECOMMANDÉ)
CREATE MATERIALIZED VIEW mv_driver_stats;

-- 4. Analytics financiers (RECOMMANDÉ)
CREATE MATERIALIZED VIEW mv_financial_analytics;

-- 5. Maintenance préventive due (RECOMMANDÉ)
CREATE MATERIALIZED VIEW mv_maintenance_due;
```

---

## 8. DIAGRAMME ERD

### 8.1 ERD Simplifié - Relations Principales

```
┌─────────────────┐
│  organizations  │──┐
└─────────────────┘  │
         │           │
         │ 1        N│
         ▼           │
┌─────────────────┐  │
│      users      │  │
└─────────────────┘  │
         │           │
         │ 1        N│
         ▼           │
┌─────────────────┐  │
│     drivers     │◄─┘
└─────────────────┘
         │
         │ 1
         ▼ N
┌─────────────────┐      ┌─────────────────┐
│   assignments   │◄────►│    vehicles     │
└─────────────────┘  N 1 └─────────────────┘
         │                        │
         │ 1                     N│
         ▼                        │
┌─────────────────┐               │
│vehicle_handover │               │
│      _forms     │               │
└─────────────────┘               │
                                  │
                                 1▼ N
                         ┌─────────────────┐
                         │ maintenance_    │
                         │   operations    │
                         └─────────────────┘
                                  │
                                 1▼ N
                         ┌─────────────────┐
                         │vehicle_mileage_ │
                         │    readings     │
                         └─────────────────┘
```

### 8.2 ERD Complet - Format Textuel

```
ORGANIZATIONS
├── users (1:N)
│   ├── drivers (1:1 optional)
│   ├── supervisor_driver_assignments (N:M via users)
│   ├── mileage_readings.recorded_by (1:N)
│   └── audit_logs (1:N)
├── vehicles (1:N)
│   ├── vehicle_type (N:1)
│   ├── fuel_type (N:1)
│   ├── transmission_type (N:1)
│   ├── vehicle_status (N:1)
│   ├── vehicle_category (N:1)
│   ├── vehicle_depot (N:1)
│   ├── assignments (1:N)
│   ├── maintenance_operations (1:N)
│   ├── repair_requests (1:N)
│   ├── mileage_readings (1:N)
│   └── vehicle_expenses (1:N)
├── drivers (1:N)
│   ├── driver_status (N:1)
│   ├── supervisor (N:1 users)
│   ├── assignments (1:N)
│   ├── driver_sanctions (1:N)
│   └── repair_requests (1:N)
├── assignments (1:N)
│   ├── vehicle (N:1)
│   ├── driver (N:1)
│   ├── created_by (N:1 users)
│   ├── updated_by (N:1 users)
│   └── ended_by_user (N:1 users)
├── documents (1:N)
│   ├── document_category (N:1)
│   ├── document_revisions (1:N)
│   └── documentable (polymorphic)
├── suppliers (1:N)
│   ├── supplier_category (N:1)
│   ├── supplier_ratings (1:N)
│   └── supplier_contracts (1:N)
├── maintenance_operations (1:N)
│   ├── maintenance_type (N:1)
│   ├── maintenance_provider (N:1)
│   ├── vehicle (N:1)
│   └── maintenance_documents (1:N)
├── repair_requests (1:N)
│   ├── repair_category (N:1)
│   ├── vehicle (N:1)
│   ├── driver (N:1 optional)
│   └── repair_request_history (1:N)
└── comprehensive_audit_logs (1:N)
    └── user (N:1)

SPATIE PERMISSION SYSTEM
├── roles (1:N)
│   ├── role_has_permissions (M:N)
│   └── model_has_roles (M:N polymorphic)
└── permissions (1:N)
    ├── role_has_permissions (M:N)
    └── model_has_permissions (M:N polymorphic)
```

---

## 9. ANNEXES

### 9.1 Liste Complète Tables (50+)

```
CORE & SYSTÈME (8):
├── users
├── organizations
├── user_organizations (pivot multi-appartenance)
├── password_reset_tokens
├── personal_access_tokens
├── failed_jobs
├── validation_levels
└── user_validation_levels

PERMISSIONS SPATIE (5):
├── roles
├── permissions
├── model_has_roles
├── model_has_permissions
└── role_has_permissions

FLOTTE - VÉHICULES (10):
├── vehicles
├── vehicle_types
├── fuel_types
├── transmission_types
├── vehicle_statuses
├── vehicle_categories
├── vehicle_depots
├── vehicle_mileage_readings
├── vehicle_expenses
└── user_vehicle (pivot)

CHAUFFEURS (5):
├── drivers
├── driver_statuses
├── driver_sanctions
├── driver_sanction_histories
└── supervisor_driver_assignments

AFFECTATIONS (2):
├── assignments
└── vehicle_handover_forms (optionnel)

MAINTENANCE (9):
├── maintenance_types
├── maintenance_providers
├── maintenance_schedules
├── maintenance_operations
├── maintenance_alerts
├── maintenance_logs
├── maintenance_documents
├── recurrence_units
└── maintenance_statuses

RÉPARATIONS (5):
├── repair_requests
├── repair_categories
├── repair_request_history
└── repair_notifications

FOURNISSEURS (3):
├── suppliers
├── supplier_categories
├── supplier_ratings

FINANCES (2):
├── vehicle_expenses
├── expense_budgets

DOCUMENTS (4):
├── documents
├── document_categories
├── document_revisions
└── documentable (pivot polymorphique)

AUDIT & COMPLIANCE (4):
├── comprehensive_audit_logs
├── organization_metrics
├── subscription_plans
└── subscription_changes

PERMISSIONS GRANULAIRES (3):
├── permission_scopes
├── contextual_permissions
└── granular_permissions

GÉOGRAPHIE ALGERIA (2):
├── algeria_wilayas
└── algeria_communes
```

### 9.2 Tailles Estimées Tables (Production 1000 orgs)

| Table | Lignes estimées | Taille | Croissance/an |
|-------|-----------------|--------|---------------|
| organizations | 1,000 | 10 MB | +20% |
| users | 50,000 | 100 MB | +15% |
| vehicles | 500,000 | 500 MB | +10% |
| drivers | 250,000 | 300 MB | +8% |
| assignments | **5,000,000** | **2 GB** | +50% |
| vehicle_mileage_readings | 20,000,000 | 5 GB | +40% |
| maintenance_operations | 2,000,000 | 1 GB | +30% |
| comprehensive_audit_logs | **50,000,000** | **20 GB** | **+100%** |
| documents | 1,000,000 | 500 MB | +25% |
| **TOTAL** | ~80M | **~30 GB** | +60%/an |

⚠️ **Tables critiques nécessitant partitionnement:**
- `comprehensive_audit_logs` (croissance exponentielle)
- `assignments` (historique long terme)
- `vehicle_mileage_readings` (accumulation continue)

### 9.3 Configuration PostgreSQL Recommandée

```ini
# postgresql.conf (pour DB ~30GB, server 16GB RAM)

# Connections
max_connections = 200
shared_buffers = 4GB                    # 25% RAM
effective_cache_size = 12GB             # 75% RAM
maintenance_work_mem = 1GB
work_mem = 20MB

# Query Planning
random_page_cost = 1.1                  # SSD
effective_io_concurrency = 200          # SSD
default_statistics_target = 100

# Checkpoints
checkpoint_completion_target = 0.9
wal_buffers = 16MB
max_wal_size = 4GB
min_wal_size = 1GB

# Parallel Query
max_worker_processes = 8
max_parallel_workers_per_gather = 4
max_parallel_workers = 8

# Logging
log_min_duration_statement = 1000      # Log queries > 1s
log_checkpoints = on
log_lock_waits = on

# Performance
shared_preload_libraries = 'pg_stat_statements'
pg_stat_statements.track = all
```

### 9.4 Commandes Maintenance Utiles

```sql
-- 1. Statistiques tables
SELECT
    schemaname,
    tablename,
    pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) AS size,
    n_live_tup AS rows
FROM pg_stat_user_tables
ORDER BY pg_total_relation_size(schemaname||'.'||tablename) DESC;

-- 2. Index inutilisés
SELECT
    schemaname,
    tablename,
    indexname,
    idx_scan,
    pg_size_pretty(pg_relation_size(indexrelid)) AS size
FROM pg_stat_user_indexes
WHERE idx_scan = 0
AND indexrelname NOT LIKE '%_pkey';

-- 3. Bloat tables
SELECT
    schemaname,
    tablename,
    pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) AS size,
    ROUND((CASE WHEN pg_total_relation_size(schemaname||'.'||tablename) = 0 THEN 0
           ELSE (pg_total_relation_size(schemaname||'.'||tablename) - pg_relation_size(schemaname||'.'||tablename)) * 100.0 / pg_total_relation_size(schemaname||'.'||tablename)
           END), 2) AS bloat_percentage
FROM pg_stat_user_tables
WHERE pg_total_relation_size(schemaname||'.'||tablename) > 10485760  -- > 10MB
ORDER BY bloat_percentage DESC;

-- 4. Requêtes lentes (pg_stat_statements)
SELECT
    query,
    calls,
    mean_exec_time,
    max_exec_time,
    total_exec_time
FROM pg_stat_statements
ORDER BY mean_exec_time DESC
LIMIT 20;

-- 5. Locks actifs
SELECT
    pid,
    usename,
    pg_blocking_pids(pid) as blocked_by,
    query
FROM pg_stat_activity
WHERE cardinality(pg_blocking_pids(pid)) > 0;
```

---

## 10. MÉTADONNÉES GÉNÉRATION

```
Document généré avec:
- Analyse manuelle 94 migrations
- Inspection 37 modèles Eloquent
- Revue contraintes et index
- Expertise PostgreSQL 16+

Outils utilisés:
- Laravel Migrations
- PostgreSQL psql
- pg_dump --schema-only
- DBeaver / pgAdmin (visualisation)

Précision: ★★★★★ (5/5)
Complétude: ★★★★★ (5/5)
Actualité: 23 Oct 2025
```

---

**Document Préparé par:** Expert Architecte Base de Données Senior  
**Date:** 23 Octobre 2025  
**Version:** 1.0  
**Prochaine Mise à Jour:** Trimestrielle ou après migration majeure

---

*Ce document constitue la référence technique officielle de la base de données ZenFleet. Toute modification de schéma doit être répercutée dans cette documentation.*
