# 📋 Guide de Test - Module de Gestion Documents

**Date :** 23 octobre 2025  
**Objectif :** Valider le module de gestion documentaire après correction de la page blanche  
**Durée estimée :** 15 minutes

---

## ✅ Pré-requis

- [x] Migrations exécutées avec succès
- [x] Cache Laravel vidé (`php artisan optimize:clear`)
- [x] Contrôleur modifié pour pointer vers Livewire
- [x] Vue `index-livewire.blade.php` créée
- [x] Navigateur actualisé (Ctrl+F5 pour forcer le rechargement)

---

## 🧪 Tests à Effectuer

### Test 1 : Accès à la Page Documents ⭐ CRITIQUE

**Action :**
1. Ouvrir le navigateur
2. Se connecter à Zenfleet
3. Accéder au menu **Documents** (ou URL : `http://localhost/admin/documents`)

**Résultat Attendu :**
```
✅ La page s'affiche complètement (pas de page blanche)
✅ Header visible : "Gestion des Documents"
✅ Sous-titre : "Gérez tous les documents de votre organisation..."
✅ Bouton "Nouveau Document" visible en haut à droite
✅ Barre de recherche présente
✅ Filtres (Catégorie, Statut) présents
✅ Tableau des documents (peut être vide si aucun document)
✅ Message "Aucun document trouvé" si la base est vide
✅ Stats en bas : "Total Documents: 0"
```

**Si la page est toujours blanche :**
```bash
# Vérifier les logs
docker compose logs --tail=100 php | grep -i "error\|exception"

# Vérifier le layout
ls -la resources/views/layouts/admin/catalyst.blade.php

# Vider à nouveau le cache navigateur
# Chrome/Edge : Ctrl + Shift + Delete
# Firefox : Ctrl + Shift + Delete
```

---

### Test 2 : Interface Utilisateur (UI)

**Action :** Observer l'interface

**Résultat Attendu :**
```
✅ Design moderne avec TailwindCSS
✅ Icône "mdi:file-document-multiple" visible dans le header
✅ Couleurs cohérentes (bleu pour primaire)
✅ Layout responsive (fonctionne sur mobile)
✅ Pas de messages d'erreur JavaScript dans la console (F12)
```

---

### Test 3 : Recherche Full-Text PostgreSQL

**Pré-requis :** Avoir au moins un document dans la base

**Action :**
1. Cliquer sur la barre de recherche
2. Taper "test" (ou le nom d'un document existant)
3. Attendre 300ms (debounce)

**Résultat Attendu :**
```
✅ Résultats filtrés instantanément
✅ Performance < 100ms
✅ Message "Aucun document trouvé" si pas de résultat
✅ Le tableau se met à jour sans rechargement de page
```

---

### Test 4 : Filtres Avancés

**Action :**
1. Sélectionner une catégorie dans le dropdown "Toutes les catégories"
2. Sélectionner un statut dans le dropdown "Tous les statuts"

**Résultat Attendu :**
```
✅ Filtres appliqués instantanément
✅ Tableau mis à jour en temps réel (Livewire)
✅ Bouton "Réinitialiser les filtres" apparaît
✅ Cliquer sur "Réinitialiser" efface tous les filtres
```

---

### Test 5 : Modal d'Upload

**Action :**
1. Cliquer sur le bouton "Nouveau Document"

**Résultat Attendu :**
```
✅ Modal s'ouvre avec animation
✅ Titre : "Nouveau Document"
✅ Zone de drag & drop visible
✅ Dropdown "Catégorie"
✅ Champs : Date émission, Date expiration, Description
✅ Boutons : "Annuler" et "Uploader"
```

**Test Upload (optionnel) :**
1. Glisser un fichier PDF/image sur la zone
2. Ou cliquer pour parcourir et sélectionner
3. Sélectionner une catégorie
4. Remplir les champs (optionnels)
5. Cliquer sur "Uploader"

**Résultat Attendu :**
```
✅ Fichier uploadé avec succès
✅ Message de succès "Document uploadé avec succès !"
✅ Modal se ferme automatiquement
✅ Le tableau se rafraîchit et affiche le nouveau document
```

---

### Test 6 : Tri des Colonnes

**Action :**
1. Cliquer sur l'en-tête "Document"
2. Cliquer à nouveau

**Résultat Attendu :**
```
✅ Première clic : tri ascendant (icône ↑)
✅ Deuxième clic : tri descendant (icône ↓)
✅ Tableau trié correctement
✅ Pas de rechargement de page (Livewire)
```

---

### Test 7 : Actions sur Documents

**Pré-requis :** Avoir au moins un document

**Actions :**
1. Observer les icônes d'actions (download, archive, delete)
2. Survoler les icônes

**Résultat Attendu :**
```
✅ Icônes visibles et colorées
✅ Tooltips apparaissent au survol
✅ Icône bleue : Télécharger
✅ Icône orange : Archiver
✅ Icône rouge : Supprimer
```

---

### Test 8 : Pagination (si > 15 documents)

**Action :**
1. Si plus de 15 documents, observer la pagination en bas
2. Cliquer sur "Page 2"

**Résultat Attendu :**
```
✅ Pagination visible
✅ Changement de page sans rechargement
✅ URL mise à jour (?page=2)
✅ Tableau rafraîchi avec nouveaux documents
```

---

### Test 9 : Stats et Métriques

**Action :** Observer la section en bas de page

**Résultat Attendu :**
```
✅ Card "Total Documents" avec icône
✅ Chiffre correct affiché
✅ Design cohérent avec le reste de l'app
```

---

### Test 10 : Performance PostgreSQL

**Action :** Ouvrir les DevTools (F12) → Network

**Résultat Attendu :**
```
✅ Temps de chargement initial < 500ms
✅ Recherche Full-Text < 100ms
✅ Changement de filtre < 200ms
✅ Pas d'erreur réseau (200 OK)
```

---

## 🐛 Problèmes Courants et Solutions

### Problème 1 : Page toujours blanche

**Solutions :**
```bash
# 1. Vérifier que la vue existe
ls resources/views/admin/documents/index-livewire.blade.php

# 2. Vérifier le layout
ls resources/views/layouts/admin/catalyst.blade.php

# 3. Vider tous les caches
docker compose exec -u zenfleet_user php php artisan optimize:clear
docker compose exec -u zenfleet_user php php artisan view:clear

# 4. Redémarrer les conteneurs
docker compose restart php
```

### Problème 2 : Composant Livewire ne se charge pas

**Solutions :**
```bash
# 1. Découvrir les composants Livewire
docker compose exec -u zenfleet_user php php artisan livewire:discover

# 2. Vérifier que le composant existe
docker compose exec -u zenfleet_user php php artisan tinker --execute="
echo class_exists('App\Livewire\Admin\DocumentManagerIndex') ? 'OK' : 'MANQUANT';
"

# 3. Vérifier les directives Livewire dans le layout
grep "@livewire" resources/views/layouts/admin/catalyst.blade.php
```

### Problème 3 : Modal ne s'ouvre pas

**Solutions :**
```bash
# 1. Vérifier Alpine.js chargé (F12 Console)
# Taper : Alpine
# Doit afficher : Object {...}

# 2. Vérifier le composant modal
ls resources/views/livewire/admin/document-upload-modal.blade.php

# 3. Vérifier l'inclusion dans index
grep "document-upload-modal" resources/views/livewire/admin/document-manager-index.blade.php
```

### Problème 4 : Recherche Full-Text ne fonctionne pas

**Solutions :**
```bash
# 1. Vérifier l'index GIN
docker compose exec -u zenfleet_user php php artisan tinker --execute="
\$indexes = DB::select('SELECT indexname FROM pg_indexes WHERE tablename = \\'documents\\' AND indexname LIKE \\'%search%\\'');
echo count(\$indexes) > 0 ? 'Index présent' : 'Index manquant';
"

# 2. Si manquant, ré-exécuter la migration
docker compose exec -u zenfleet_user php php artisan migrate:fresh --force
```

---

## ✅ Checklist de Validation Complète

### Affichage et UI
- [ ] Page s'affiche complètement (pas blanche)
- [ ] Header et sous-titre visibles
- [ ] Bouton "Nouveau Document" présent
- [ ] Design moderne et cohérent
- [ ] Responsive (testé sur mobile)

### Fonctionnalités de Base
- [ ] Barre de recherche fonctionne
- [ ] Filtres (catégorie, statut) fonctionnent
- [ ] Bouton "Réinitialiser" fonctionne
- [ ] Tri des colonnes fonctionne

### Modal d'Upload
- [ ] Modal s'ouvre au clic
- [ ] Drag & drop fonctionne
- [ ] Upload de fichier fonctionne
- [ ] Validation des champs fonctionne
- [ ] Message de succès s'affiche
- [ ] Tableau se rafraîchit après upload

### Actions sur Documents
- [ ] Icônes visibles et correctes
- [ ] Téléchargement fonctionne
- [ ] Archivage fonctionne
- [ ] Suppression fonctionne (avec confirmation)
- [ ] Tooltips s'affichent

### Performance
- [ ] Chargement initial < 500ms
- [ ] Recherche Full-Text < 100ms
- [ ] Pas d'erreur console (F12)
- [ ] Pas d'erreur réseau

### Pagination et Stats
- [ ] Pagination fonctionne (si applicable)
- [ ] Stats affichent le bon nombre
- [ ] Message "Aucun document" si base vide

---

## 📊 Rapport de Test à Compléter

```
Date du test : _____________________
Testeur : __________________________
Environnement : [ ] Local [ ] Dev [ ] Staging [ ] Prod

RÉSULTATS :
✅ Tests réussis : __ / 10
❌ Tests échoués : __ / 10

NOTES :
_________________________________________
_________________________________________
_________________________________________

BUGS IDENTIFIÉS :
1. _____________________________________
2. _____________________________________
3. _____________________________________

STATUT FINAL :
[ ] ✅ MODULE VALIDÉ - Production Ready
[ ] ⚠️ MODULE FONCTIONNEL - Corrections mineures nécessaires
[ ] ❌ MODULE NON FONCTIONNEL - Corrections majeures requises
```

---

## 🚀 Prochaines Étapes Après Validation

### Si Tous les Tests Passent (✅)

1. **Mettre en production**
   ```bash
   # Backup base de données
   docker compose exec -u zenfleet_user php php artisan db:backup
   
   # Déployer
   git add .
   git commit -m "feat: Module gestion documents enterprise-grade validé"
   git push
   ```

2. **Former les utilisateurs**
   - Session de formation (1h)
   - Documentation utilisateur
   - Vidéo tutoriel

3. **Activer le monitoring**
   - Logs d'accès
   - Métriques de performance
   - Alertes sur erreurs

### Si Des Tests Échouent (❌)

1. **Identifier la cause**
   - Vérifier les logs
   - Reproduire le bug
   - Documenter le problème

2. **Corriger et re-tester**
   - Appliquer le fix
   - Vider les caches
   - Re-exécuter les tests

3. **Valider la correction**
   - Tous les tests doivent passer
   - Pas de régression

---

**Bon test ! 🧪**

---

*Ce guide fait partie de la documentation du module de gestion documentaire Zenfleet.*
