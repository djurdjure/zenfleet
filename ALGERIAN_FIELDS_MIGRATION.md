# 🇩🇿 Migration des Champs Algériens

## ✅ Migration Créée

**Fichier**: `database/migrations/2025_09_15_020000_add_algerian_fields_to_organizations.php`

### Champs Algériens Ajoutés

#### 1. **Identifiants Légaux Algériens**
- `nif` (varchar 15) - Numéro d'Identification Fiscale (15 chiffres)
- `ai` (varchar 14) - Article d'Imposition (14 chiffres)
- `nis` (varchar 15) - Numéro d'Identification Statistique (15 chiffres)
- `trade_register` (varchar 50) - Registre de commerce (format: WW/YY-XXXXXX B SS)

#### 2. **Division Administrative**
- `wilaya` (varchar 2) - Code wilaya algérienne (01-58)

#### 3. **Responsable Légal/Gérant**
- `manager_name` - Nom et prénom du responsable légal
- `manager_nin` (varchar 18) - NIN du responsable (18 chiffres)
- `manager_function` (enum) - Fonction: gerant, directeur_general, president, etc.
- `manager_email` - Email du responsable légal

#### 4. **Documents Scannés**
- `scan_nif_path` - Chemin du scan du NIF
- `scan_ai_path` - Chemin du scan de l'AI
- `scan_nis_path` - Chemin du scan du NIS
- `scan_manager_cin_path` - Chemin du scan CIN du responsable
- `scan_manager_mandate_path` - Chemin procuration/mandat

#### 5. **Index de Performance**
- `org_location_algeria_idx` - Index composé (wilaya, city)
- `org_nif_status_idx` - Index composé (nif, status)
- `org_trade_register_idx` - Index simple (trade_register)

## 🚀 Test de la Migration

```bash
# Test complet migration + seeding
docker compose exec -u zenfleet_user php php artisan migrate:fresh --seed
```

## 📋 Résultat Attendu

### 1. **Migration Réussie**
- ✅ Table organizations avec tous les champs algériens
- ✅ Index de performance créés
- ✅ Contraintes et commentaires appliqués

### 2. **Seeding Réussi**
- ✅ OrganizationFactory utilise les champs algériens
- ✅ 3 organisations créées avec données algériennes réalistes
- ✅ 18 utilisateurs avec rôles assignés
- ✅ Système de permissions fonctionnel

### 3. **Données Algériennes Générées**
```
ZenFleet Platform:
- NIF: 123456789012345
- Trade Register: 16/23-123456 B 10
- Wilaya: 16 (Alger)
- Responsable: Ahmed Benali

TransAlger SARL:
- NIF: XXX (généré)
- Wilaya: 16 (Alger)
- Responsable: Karim Abdellah

LogistiqueOran SPA:
- NIF: XXX (généré)
- Wilaya: 31 (Oran)
- Responsable: Amina Bensaid
```

## 🎯 Avantages

1. **Conformité Légale** - Tous les identifiants fiscaux algériens
2. **Géolocalisation** - Système de wilayas intégré
3. **Documents** - Stockage des scans officiels
4. **Performance** - Index optimisés pour l'Algérie
5. **Extensibilité** - Base solide pour futures fonctionnalités

## 🔄 Si Erreur
La migration vérifie l'existence des colonnes avant ajout, donc safe à réexécuter.