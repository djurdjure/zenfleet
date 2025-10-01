# 🚀 Guide Utilisateur - Nouveau Module Maintenance

## 📋 TABLE DES MATIÈRES
1. [Accéder au nouveau menu](#1-accéder-au-nouveau-menu)
2. [Page Surveillance](#2-page-surveillance)
3. [Ajouter une opération de maintenance](#3-ajouter-une-opération-de-maintenance)
4. [Types de maintenance disponibles](#4-types-de-maintenance-disponibles)
5. [FAQ et Dépannage](#5-faq-et-dépannage)

---

## 1. Accéder au nouveau menu

### Étape 1 : Vider le cache du navigateur
**IMPORTANT** : Pour voir les changements, vous DEVEZ vider le cache de votre navigateur.

**Chrome/Edge/Brave** :
1. Appuyez sur `Ctrl + Shift + Delete` (Windows/Linux) ou `Cmd + Shift + Delete` (Mac)
2. Cochez "Images et fichiers en cache"
3. Cliquez sur "Effacer les données"

**Firefox** :
1. Appuyez sur `Ctrl + Shift + Delete` (Windows/Linux) ou `Cmd + Shift + Delete` (Mac)
2. Cochez "Cache"
3. Cliquez sur "Effacer maintenant"

### Étape 2 : Forcer le rechargement de la page
- **Windows/Linux** : `Ctrl + F5`
- **Mac** : `Cmd + Shift + R`

### Étape 3 : Navigation dans le menu
1. Connectez-vous à ZenFleet
2. Dans le menu latéral gauche, trouvez **"Maintenance"**
3. Cliquez dessus pour déplier les sous-menus
4. Vous devriez voir :
   - 🖥️ **Surveillance**
   - 📅 **Planifications**
   - 🛠️ **Demandes réparation**
   - ⚙️ **Opérations**

---

## 2. Page Surveillance

### Accès
Menu latéral > Maintenance > **Surveillance**

URL directe : `http://localhost/admin/maintenance/surveillance`

### Fonctionnalités

#### Statistiques en temps réel
La page affiche 3 métriques principales :
- **En cours** : Nombre de maintenances actuellement en cours
- **Proches (7 jours)** : Maintenances à effectuer dans les 7 prochains jours
- **À échéance** : Maintenances en retard

#### Tableau de surveillance
Colonnes affichées :
- **Urgence** : Badge coloré (Critique/Urgent/Attention/Normal)
- **Véhicule** : Plaque d'immatriculation + marque/modèle
- **Type Maintenance** : Type de maintenance à effectuer
- **Statut** : État actuel (Terminé/En cours/Planifié)
- **Échéance** : Date prévue
- **Jours restants** : Nombre de jours en **chiffres arrondis** (ex: 5 jours, pas 5.3)
- **Actions** : Voir détails / Modifier

#### Filtres disponibles
1. **Par période** :
   - Toutes
   - Aujourd'hui
   - Cette semaine
   - Ce mois
   - En retard

2. **Par statut** :
   - Tous
   - Terminées
   - En retard
   - En cours
   - Planifiées

#### Utilisation des filtres
1. Sélectionnez un filtre dans les menus déroulants
2. Cliquez sur "Filtrer"
3. Pour réinitialiser : cliquez sur "Réinitialiser"

---

## 3. Ajouter une opération de maintenance

### Méthode 1 : Depuis la page Surveillance
1. Cliquez sur le bouton **"Nouvelle opération"** en haut à droite
2. Le formulaire de création s'ouvre

### Méthode 2 : Depuis le menu
1. Menu > Maintenance > **Opérations**
2. Cliquez sur "Ajouter une opération"

### Remplir le formulaire

#### Champ "Type de maintenance"
**NOUVEAU** : Les types sont maintenant **prédéfinis** et apparaissent automatiquement dans la liste !

Types disponibles :
1. **Inspection** (Documents administratifs)
   - Renouvellement assurance
   - Assurance marchandises
   - Vignette/impôts
   - Contrôle technique périodique

2. **Préventive** (Maintenance régulière)
   - Vidange huile moteur
   - Remplacement filtres
   - Contrôle/courroie de distribution ou chaîne
   - Rotation/permutation des pneus
   - Test/remplacement batterie
   - Contrôle éclairage et signalisation
   - Remplacement balais d'essuie-glace
   - Contrôle mécanique
   - Contrôle électricité
   - Contrôle des Freins

3. **Corrective** (Réparations)
   - Autres

#### Autres champs
- **Véhicule** : Sélectionnez le véhicule concerné
- **Date prévue** : Date de l'intervention
- **Fournisseur** : Garage ou prestataire (optionnel)
- **Description** : Détails de l'opération
- **Coût estimé** : Budget prévu (optionnel)

---

## 4. Types de maintenance disponibles

### 📋 Liste complète par catégorie

#### 📄 Inspection (Administratif)
| Type | Fréquence | Durée estimée |
|------|-----------|---------------|
| Renouvellement assurance | Annuel (365j) | 30 min |
| Assurance marchandises | Annuel (365j) | 30 min |
| Vignette/impôts | Annuel (365j) | 1h |
| Contrôle technique | 2 ans (730j) | 2h |

#### 🔧 Préventive (Entretien régulier)
| Type | Fréquence | Durée estimée |
|------|-----------|---------------|
| Vidange huile moteur | 10 000 km / 1 an | 1h |
| Remplacement filtres | 15 000 km / 1 an | 45 min |
| Courroie distribution | 60 000 km / 5 ans | 4h |
| Rotation pneus | 10 000 km / 6 mois | 30 min |
| Test batterie | 2 ans | 30 min |
| Contrôle éclairage | 15 000 km / 6 mois | 30 min |
| Balais essuie-glace | Annuel | 15 min |
| Contrôle mécanique | 20 000 km / 1 an | 2h |
| Contrôle électricité | 20 000 km / 1 an | 1h30 |
| Contrôle freins | 20 000 km / 1 an | 1h30 |

#### 🛠️ Corrective (Réparations)
| Type | Fréquence | Durée estimée |
|------|-----------|---------------|
| Autres | Variable | 1h |

---

## 5. FAQ et Dépannage

### ❓ Le menu ne s'affiche pas correctement

**Solution 1** : Vider le cache du navigateur
```
1. Ctrl + Shift + Delete (Cmd + Shift + Delete sur Mac)
2. Cocher "Cache" et "Cookies"
3. Effacer
4. Fermer et rouvrir le navigateur
```

**Solution 2** : Mode navigation privée
```
Ouvrez l'application en mode navigation privée/incognito
Si ça fonctionne, c'est bien un problème de cache
```

**Solution 3** : Tester avec un autre navigateur
```
Chrome → Firefox
Edge → Chrome
Safari → Chrome
```

### ❓ Les types de maintenance n'apparaissent pas dans la liste

**Vérification** :
```bash
# Connectez-vous au serveur et exécutez :
docker compose exec -u zenfleet_user php php artisan tinker --execute="echo App\Models\MaintenanceType::count();"
```

Si le résultat est **0** ou **aucun type pour votre organisation** :
```bash
# Exécutez le seeder :
docker compose exec -u zenfleet_user php php artisan db:seed --class=MaintenanceTypesSeeder
```

### ❓ Erreur 404 sur la page Surveillance

**Solution** :
```bash
# Vider le cache des routes
docker compose exec -u zenfleet_user php php artisan route:clear
docker compose exec -u zenfleet_user php php artisan route:cache
```

### ❓ La page se charge mais sans données

**Vérification des logs** :
```bash
# Voir les dernières erreurs
docker compose exec -u zenfleet_user php tail -50 storage/logs/laravel.log
```

**Causes possibles** :
1. Problème de connexion à la base de données
2. Organisation non assignée à l'utilisateur
3. Permissions insuffisantes

### ❓ Le menu s'affiche mais les liens ne fonctionnent pas

**Solution** :
```bash
# Rebuild complet
docker compose exec -u zenfleet_user php php artisan optimize:clear
docker compose exec -u zenfleet_user node yarn build
docker compose restart
```

---

## 📞 Support

### Logs à vérifier
1. **Laravel** : `storage/logs/laravel.log`
2. **Nginx** : `storage/logs/nginx/error.log`
3. **PHP-FPM** : Logs Docker

### Commande de diagnostic
```bash
bash test_menu_maintenance.sh
```

Ce script vérifie automatiquement :
- ✅ Fichiers modifiés
- ✅ Routes créées
- ✅ Types de maintenance
- ✅ Contrôleurs corrigés

---

## 🎯 Checklist de vérification

Avant de contacter le support, vérifiez que vous avez bien :

- [ ] Vidé le cache du navigateur (Ctrl+Shift+Delete)
- [ ] Forcé le rechargement de la page (Ctrl+F5)
- [ ] Vérifié que vous êtes connecté avec un compte autorisé (Admin/Gestionnaire)
- [ ] Testé avec un autre navigateur
- [ ] Exécuté les commandes de cache Laravel
- [ ] Vérifié que les types de maintenance existent (tinker)
- [ ] Consulté les logs Laravel pour les erreurs

---

**Version** : 2.0
**Date** : 30/09/2025
**Auteur** : Expert Laravel - 20 ans d'expérience