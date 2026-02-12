# Algeria-Centric Refactor Runbook

## Overview

This runbook documents the complete refactoring of ZenFleet from an international fleet management system to an Algeria-centric deployment. The changes eliminate multi-currency, global timezone, and country constructs in favor of Algeria administrative units (wilayas), DZD currency standardization, and proper Algeria business identifiers.

## Root Cause Analysis

**Issues Identified:**
1. **Enhanced RBAC Migration** (`2025_09_06_101000_create_enhanced_rbac_system.php`) introduced international fields:
   - `currency` field defaulting to EUR instead of DZD
   - Per-organization `timezone` and `country_code` fields
   - Multi-country support where single-country (Algeria) deployment is required

2. **Form Request Validation** (`StoreOrganizationRequest.php`) expected:
   - Country/currency/timezone inputs
   - International SIRET/VAT validation instead of Algeria NIF/AI/NIS

3. **UI Components** passed empty arrays for countries/currencies/timezones but components still referenced them

**Impact:**
- Unnecessary complexity in UI forms
- Database bloat with unused international fields
- Inconsistent validation rules
- Poor UX for Algeria-specific business processes

## Solution Architecture

**Database Changes:**
- Remove `currency`, `timezone`, `country_code` from organizations
- Remove per-user `timezone` (standardize to Africa/Algiers at app level)
- Add `commune` field for precise Algeria location data
- Create `algeria_wilayas` and `algeria_communes` lookup tables
- Standardize to 2-character wilaya codes (01-48)

**Application Changes:**
- New Algeria-centric Form Request with proper validation
- Algeria-specific form component with wilaya dropdown
- Realistic test data factories with Algeria business identifiers
- Configuration helper for Algeria defaults

## Files Changed

### Database Migrations
- `database/migrations/2025_01_19_100000_remove_international_artifacts_standardize_algeria.php`

### Models
- `app/Models/Organization.php` - Updated fillable fields, settings defaults, relationships
- `app/Models/AlgeriaWilaya.php` - New model for Algeria wilayas
- `app/Models/AlgeriaCommune.php` - New model for Algeria communes

### Form Requests
- `app/Http/Requests/Admin/StoreOrganizationAlgeriaRequest.php` - Algeria-centric validation

### Controllers
- `app/Http/Controllers/Admin/OrganizationController.php` - Updated to use new request, provide wilayas

### Views
- `resources/views/admin/organizations/create.blade.php` - Updated to use Algeria component
- `resources/views/components/organization-form-algeria.blade.php` - New Algeria-centric form

### Factories & Seeders
- `database/factories/OrganizationFactory.php` - Updated for Algeria data
- `database/seeders/AlgeriaOrganizationSeeder.php` - Deterministic test organizations
- `database/seeders/AlgeriaFleetSeeder.php` - Realistic vehicles and drivers
- `database/seeders/DatabaseSeeder.php` - Updated to use Algeria seeders

### Tests
- `tests/Feature/Admin/OrganizationAlgeriaTest.php` - Feature tests for forms and validation
- `tests/Unit/AlgeriaWilayaTest.php` - Unit tests for wilaya model
- `tests/Unit/AlgeriaOrganizationTest.php` - Unit tests for updated organization model

### Configuration
- `config/algeria.php` - Algeria-specific defaults and constants

## Execution Commands

### Prerequisites
Ensure Docker containers are running:
```bash
docker-compose up -d
```

### Step 1: Run Migrations
```bash
# Apply the Algeria-centric migration
docker-compose exec app php artisan migrate

# Verify migration status
docker-compose exec app php artisan migrate:status
```

### Step 2: Seed Algeria Data
```bash
# Seed Algeria wilayas, communes, and test organizations
docker-compose exec app php artisan db:seed --class=AlgeriaOrganizationSeeder

# Seed realistic fleet data (vehicles and drivers)
docker-compose exec app php artisan db:seed --class=AlgeriaFleetSeeder

# Or run all seeders
docker-compose exec app php artisan db:seed
```

### Step 3: Run Tests
```bash
# Run Algeria-specific tests
docker-compose exec app php artisan test --filter=Algeria

# Run all organization tests
docker-compose exec app php artisan test tests/Feature/Admin/OrganizationAlgeriaTest.php

# Run all tests
docker-compose exec app php artisan test
```

### Step 4: Build Assets (if views changed)
```bash
# Install dependencies
docker-compose exec app yarn install

# Build for development
docker-compose exec app yarn dev

# Or build for production
docker-compose exec app yarn build
```

### Step 5: Clear Caches
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan route:clear
```

## Validation Steps

### 1. Database Validation
```bash
# Check that international fields are removed
docker-compose exec app php artisan tinker
>>> \App\Models\Organization::first()->getAttributes();
# Should NOT contain: currency, timezone, country_code

# Check Algeria wilayas are seeded
>>> \App\Models\AlgeriaWilaya::count();
# Should return 48

# Check organizations have valid wilaya codes
>>> \App\Models\Organization::whereNotIn('wilaya', \App\Models\AlgeriaWilaya::pluck('code'))->count();
# Should return 0
```

### 2. UI Validation
1. Navigate to `/admin/organizations/create`
2. Verify form shows:
   - Wilaya dropdown with 48 Algeria wilayas
   - NIF/AI/NIS fields instead of international tax fields
   - Algeria phone number format validation
   - No country/currency/timezone selectors

### 3. Data Validation
```bash
# Check test data quality
docker-compose exec app php artisan tinker
>>> $org = \App\Models\Organization::with('wilayaInfo')->first();
>>> $org->wilayaInfo->name_fr; // Should show wilaya name
>>> $org->phone_number; // Should start with +213
>>> $org->manager_nin; // Should be 18 digits
>>> $org->settings; // Should have locale: 'ar', phone_format: '+213'
```

### 4. Form Validation
Test the form with various inputs:
- Invalid wilaya code (should fail)
- Invalid Algeria phone number (should fail)
- Invalid NIN format (should fail)
- Valid Algeria data (should succeed)

### 5. Seeded Data Verification
```bash
# Check baseline organizations
docker-compose exec app php artisan tinker
>>> \App\Models\Organization::where('name', 'Trans-Alger Logistics')->exists(); // true
>>> \App\Models\Organization::count(); // Should be 12 (3 baseline + 9 random)

# Check vehicles and drivers
>>> \App\Models\Vehicle::count(); // Should have vehicles
>>> \App\Models\Driver::count(); // Should have drivers
>>> \App\Models\Vehicle::first()->license_plate; // Should match Algeria format
```

## Rollback Plan

If issues arise, rollback using migration:
```bash
# Rollback the Algeria migration
docker-compose exec app php artisan migrate:rollback --step=1

# Restore international fields
# (This will restore currency, timezone, country_code fields)

# Clear caches
docker-compose exec app php artisan cache:clear
```

## Data Migration Notes

**Safe Operations:**
- Adding commune field (nullable)
- Removing unused international fields
- Normalizing wilaya codes to 2-character format

**Destructive Operations:**
- Dropping currency/timezone/country fields (ensure no dependencies)
- Changing wilaya from text to 2-character code (data migration included)

**Data Preserved:**
- All existing organization data
- Algerian business identifiers (NIF, AI, NIS)
- Manager information
- Core business logic unchanged

## Testing Matrix

| Test Case | Expected Result |
|-----------|----------------|
| Create organization with valid Algeria data | Success, all fields saved correctly |
| Invalid wilaya code | Validation error |
| Invalid phone format | Validation error, suggests +213 format |
| Invalid NIN (not 18 digits) | Validation error |
| File uploads (NIF scan, logo) | Files stored in public/organizations/ |
| Wilaya relationship | Organization linked to AlgeriaWilaya model |
| Settings defaults | locale: 'ar', phone_format: '+213' |
| API response | Contains wilaya, excludes international fields |

## Post-Deployment Checklist

- [ ] All migrations applied successfully
- [ ] Algeria lookup data seeded (48 wilayas)
- [ ] Test organizations created (12 total)
- [ ] Fleet data populated (vehicles and drivers)
- [ ] Tests passing (Algeria-specific and existing)
- [ ] UI forms show Algeria-specific fields
- [ ] Phone number validation works for +213 format
- [ ] File uploads functional
- [ ] No console errors in browser
- [ ] Cache cleared and application responsive

## Maintenance Notes

**Regular Tasks:**
- Monitor for new Algeria administrative changes
- Update commune data if needed
- Ensure new features follow Algeria-centric patterns

**Performance Considerations:**
- Algeria lookup tables are small (48 wilayas, ~100 sample communes)
- Indexes added for wilaya filtering
- No impact on existing query performance

**Security Notes:**
- Algeria NIN/NIF validation prevents injection
- File upload restrictions maintained
- Phone number normalization prevents malformed data