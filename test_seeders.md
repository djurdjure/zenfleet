# Test des Seeders ZenFleet

## Problèmes Résolus

### 1. Syntaxe PostgreSQL
- ❌ **Erreur**: `ALTER TABLE organizations COMMENT = '...'` (syntaxe MySQL)
- ✅ **Correction**: `COMMENT ON TABLE organizations IS '...'` (syntaxe PostgreSQL)

### 2. Simplification des Factories
- ❌ **Problème**: UserFactory utilisait des champs qui n'existent pas encore
- ✅ **Solution**: Simplified pour utiliser seulement les champs de base:
  - `name`, `first_name`, `last_name`, `email`, `phone`
  - Supprimé: `uuid`, `organization_id`, `job_title`, etc.

### 3. Simplification du DatabaseSeeder
- ❌ **Problème**: Références à des champs inexistants
- ✅ **Solution**: Version simplifiée qui crée:
  - 1 Super Admin (ZenFleet)
  - 2 organisations de test
  - 17 utilisateurs au total avec rôles assignés

## Commandes de Test

```bash
# Dans Docker
docker compose exec -u zenfleet_user php php artisan migrate:fresh --seed

# Alternative (si la première échoue)
docker compose exec -u zenfleet_user php php artisan migrate:fresh
docker compose exec -u zenfleet_user php php artisan db:seed
```

## Données Créées

### Organisation ZenFleet
- **Super Admin**: `superadmin@zenfleet.dz` / `password`

### Organisation TransAlger (Équipe complète - 12 utilisateurs)
- **Admin**: `admin@transalger.dz`
- **Gestionnaire Flotte**: `flotte@transalger.dz`
- **2 Superviseurs**: `superviseur1@transalger.dz`, `superviseur2@transalger.dz`
- **5 Chauffeurs**: `chauffeur1@transalger.dz` → `chauffeur5@transalger.dz`
- **Comptable**: `comptable@transalger.dz`
- **Mécanicien**: `mecanicien@transalger.dz`

### Organisation LogistiqueOran (Équipe réduite - 5 utilisateurs)
- **Admin**: `admin@logistiqueoran.dz`
- **Gestionnaire Flotte**: `flotte@logistiqueoran.dz`
- **Superviseur**: `superviseur@logistiqueoran.dz`
- **2 Chauffeurs**: `chauffeur1@logistiqueoran.dz`, `chauffeur2@logistiqueoran.dz`

## Vérification

Une fois les seeders exécutés, vérifier:

1. **Organisations créées**: 3 organisations au total
2. **Utilisateurs créés**: 18 utilisateurs au total
3. **Rôles assignés**: Tous les utilisateurs ont leurs rôles
4. **Permissions**: 65 permissions + 8 rôles créés

## Prochaines Étapes

Une fois que les seeders de base fonctionnent:
1. Ajouter les champs manquants aux migrations users
2. Enrichir les factories avec plus de données
3. Ajouter véhicules et autres données de test