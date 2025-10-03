# ⚡ DÉPLOIEMENT RAPIDE - Fix Multi-Tenant Véhicules

## 🎯 PROBLÈME
- Erreur SQL "duplicate key" lors d'import véhicules
- Contrainte unique globale empêche véhicules dans plusieurs orgs
- Messages d'erreur techniques non user-friendly

## ✅ SOLUTION
- Contraintes uniques SCOPED par organisation
- Messages d'erreur clairs en français
- Support ventes véhicules entre organisations

---

## 🚀 DÉPLOIEMENT (3 MINUTES)

### **Étape 1: Exécuter la Migration**

```bash
# 🐳 Avec Docker (Recommandé)
docker compose exec -u zenfleet_user php php artisan migrate

# 💻 Sans Docker
php artisan migrate
```

**Résultat attendu:**
```
Migrating: 2025_10_03_140000_fix_vehicles_unique_constraints_multitenant
Migrated:  2025_10_03_140000_fix_vehicles_unique_constraints_multitenant
```

---

### **Étape 2: Vider le Cache**

```bash
# 🐳 Avec Docker
docker compose exec -u zenfleet_user php php artisan optimize:clear

# 💻 Sans Docker
php artisan optimize:clear
```

---

### **Étape 3: Tester**

```bash
# Script de test automatique
docker compose exec -u zenfleet_user php php test_multi_tenant_vehicles.php

# Doit afficher:
# ✅ NOUVEAU SYSTÈME: Contraintes multi-tenant actives
# ✅ Aucun doublon intra-organisation (CORRECT)
# ✅ Multi-tenant fonctionne: Même plaque autorisée dans Org X
```

---

## 🧪 TESTS MANUELS

### **Test 1: Import Véhicule Doublon (Même Org)**

1. Login: `admin@faderco.dz`
2. Créer véhicule: Plaque "AB-123-CD"
3. Importer CSV avec "AB-123-CD"
4. ✅ Message: **"Véhicule déjà existant dans votre organisation (plaque: AB-123-CD)"**
   - ❌ **AVANT**: "SQLSTATE[23505]: Unique violation..."

### **Test 2: Import Véhicule Doublon (Org Différente)**

1. Login: `superadmin@zenfleet.com` (Org 1)
2. Créer véhicule: Plaque "AB-123-CD"
3. Login: `admin@faderco.dz` (Org 3)
4. Importer CSV avec "AB-123-CD"
5. ✅ **Import RÉUSSI** (véhicule créé dans Org 3)
   - ❌ **AVANT**: Erreur "duplicate key"

---

## 📁 FICHIERS MODIFIÉS

| Fichier | Description |
|---------|-------------|
| **`2025_10_03_140000_fix_vehicles_unique_constraints_multitenant.php`** | Migration contraintes multi-tenant |
| **`VehicleController.php`** | Vérification doublons scoped + messages clairs |

---

## 🔍 VÉRIFICATION CONTRAINTES

### **PostgreSQL Direct**

```bash
# Connexion DB
docker compose exec postgres psql -U zenfleet_user -d zenfleet

# Vérifier contraintes
SELECT conname
FROM pg_constraint
WHERE conname LIKE '%vehicles%'
  AND conname LIKE '%unique%';

# Doit afficher:
# vehicles_registration_plate_organization_unique ✅
# vehicles_vin_organization_unique ✅
```

---

## 🆘 DÉPANNAGE

### **Erreur: Contrainte existe déjà**

```bash
# Supprimer anciennes contraintes manuellement
docker compose exec postgres psql -U zenfleet_user -d zenfleet -c "
  ALTER TABLE vehicles DROP CONSTRAINT IF EXISTS vehicles_registration_plate_unique;
  ALTER TABLE vehicles DROP CONSTRAINT IF EXISTS vehicles_vin_unique;
"

# Réexécuter migration
docker compose exec -u zenfleet_user php php artisan migrate
```

### **Erreur: Messages toujours SQL bruts**

```bash
# Vider tous les caches
docker compose exec -u zenfleet_user php php artisan optimize:clear

# Redémarrer PHP-FPM
docker compose restart php
```

---

## ✅ CHECKLIST

- [ ] Migration exécutée (`php artisan migrate`)
- [ ] Cache vidé (`php artisan optimize:clear`)
- [ ] Script test exécuté (`php test_multi_tenant_vehicles.php`)
- [ ] Test manuel 1 validé (doublon même org)
- [ ] Test manuel 2 validé (doublon org différente)
- [ ] Contraintes DB vérifiées (PostgreSQL)

---

## 📊 RÉSULTAT FINAL

### **AVANT ❌**
```
Import véhicule "AB-123-CD" dans Org 3
→ ERREUR si existe dans Org 1
→ Message: "SQLSTATE[23505]: Unique violation..."
```

### **APRÈS ✅**
```
Import véhicule "AB-123-CD" dans Org 3
→ SUCCÈS même si existe dans Org 1
→ Message: "Import réussi: 1 véhicule(s)"

Doublon dans Org 3
→ Message clair: "Véhicule déjà existant dans votre organisation"
```

---

**Temps estimé:** 3 minutes
**Difficulté:** ⭐ Facile
**Impact:** 🚀 Critique (Multi-Tenant)
