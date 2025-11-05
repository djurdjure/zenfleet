# 🧪 GUIDE DE TEST MANUEL - MODULE DÉPÔTS
**Version**: Enterprise-Grade v1.0  
**Date**: 2025-11-05  
**Objectif**: Valider les corrections des bugs critiques

---

## ✅ PRÉ-REQUIS

1. Migrations appliquées : `php artisan migrate`
2. Serveur démarré : Vérifier que `zenfleet_php` est UP
3. Accès navigateur : URL du module dépôts

---

## 🧪 TEST 1 : CRÉATION AVEC CODE AUTO-GÉNÉRÉ

### Objectif
Valider que les dépôts se créent **SANS code** et que le code est **auto-généré**.

### Étapes
1. Aller sur la page **Gestion des Dépôts**
2. Cliquer sur **"Nouveau Dépôt"**
3. Remplir le formulaire :
   - **Nom** : `Test Auto-Gen 1`
   - **Ville** : `Alger`
   - **Wilaya** : `Alger`
   - **Capacité** : `50`
   - ⚠️ **NE PAS remplir le champ "Code"** (laisser vide)
   - **Dépôt actif** : Coché
4. Cliquer sur **"Créer"**

### ✅ Résultats Attendus
- ✅ Le modal se **ferme**
- ✅ Message de succès : **"Dépôt créé avec succès"**
- ✅ Le dépôt apparaît dans la liste avec un code **DP0001**
- ✅ Le dépôt est marqué comme **Actif** (badge vert)

### ❌ Résultats à Éviter (Bug Corrigé)
- ❌ Le modal reste ouvert sans message
- ❌ Aucun dépôt n'apparaît dans la liste
- ❌ Message d'erreur "code cannot be null"

---

## 🧪 TEST 2 : CRÉATION AVEC CODE PERSONNALISÉ

### Objectif
Valider qu'on peut toujours créer des dépôts avec un **code personnalisé**.

### Étapes
1. Cliquer sur **"Nouveau Dépôt"**
2. Remplir le formulaire :
   - **Nom** : `Test Perso 1`
   - **Code** : `CUSTOM-01`
   - **Ville** : `Oran`
   - **Capacité** : `30`
3. Cliquer sur **"Créer"**

### ✅ Résultats Attendus
- ✅ Le dépôt se crée avec le code **CUSTOM-01**
- ✅ Message de succès visible
- ✅ Dépôt affiché dans la liste

---

## 🧪 TEST 3 : TOGGLE "DÉPÔT ACTIF" (FIX UX)

### Objectif
Valider qu'**aucun espace** ne se crée quand on clique sur le toggle.

### Étapes
1. Cliquer sur **"Nouveau Dépôt"**
2. Remplir **uniquement le champ "Nom"** : `Test Toggle`
3. Observer le **formulaire AVANT** de cliquer sur le toggle
4. Cliquer sur le **toggle "Dépôt actif"** plusieurs fois (ON → OFF → ON)
5. Observer si le **bouton "Créer"** bouge ou si un **espace apparaît**

### ✅ Résultats Attendus (Fix Appliqué)
- ✅ **AUCUN espace** ne se crée sous le bouton "Créer"
- ✅ Le bouton **reste stable** à sa position
- ✅ Le toggle et les boutons sont **alignés horizontalement**
- ✅ Transition **fluide** sans saut visuel

### ❌ Résultats à Éviter (Bug Corrigé)
- ❌ Un espace blanc apparaît sous le bouton
- ❌ Le bouton "Créer" bouge vers le bas
- ❌ Saut visuel lors du clic sur le toggle

---

## 🧪 TEST 4 : GESTION DES ERREURS

### Objectif
Valider que le **modal reste ouvert** en cas d'erreur.

### Étapes - Test Code Dupliqué
1. Créer un dépôt avec le code **DUPLICATE-01**
2. Essayer de créer un **autre dépôt** avec le **même code** `DUPLICATE-01`
3. Cliquer sur **"Créer"**

### ✅ Résultats Attendus (Fix Appliqué)
- ✅ Le **modal reste ouvert** (ne se ferme pas)
- ✅ Message d'erreur **visible dans le modal** :
  ```
  ⚠️ Erreur lors de l'enregistrement : ... duplicate key ...
  ```
- ✅ L'utilisateur peut **corriger le code** sans tout ressaisir
- ✅ Les données du formulaire sont **préservées**

### ❌ Résultats à Éviter (Bug Corrigé)
- ❌ Le modal se ferme automatiquement
- ❌ Aucun message d'erreur visible
- ❌ L'utilisateur perd toutes ses données

---

## 🧪 TEST 5 : LOADING STATE

### Objectif
Valider le **feedback visuel** lors de l'enregistrement.

### Étapes
1. Cliquer sur **"Nouveau Dépôt"**
2. Remplir rapidement : **Nom** = `Test Loading`
3. Cliquer sur **"Créer"**
4. Observer le **bouton pendant l'enregistrement**

### ✅ Résultats Attendus
- ✅ Le bouton affiche **"Enregistrement..."** avec un **spinner**
- ✅ Le bouton est **désactivé** pendant l'enregistrement
- ✅ Impossible de cliquer plusieurs fois (prévention double-submit)

---

## 🧪 TEST 6 : SÉQUENCE AUTO-GÉNÉRATION

### Objectif
Valider que les codes auto-générés sont **séquentiels**.

### Étapes
1. Créer **3 dépôts consécutifs** SANS remplir le code :
   - Dépôt 1 : `Auto Test 1` → Code attendu : `DP0001` (ou suivant)
   - Dépôt 2 : `Auto Test 2` → Code attendu : `DP0002`
   - Dépôt 3 : `Auto Test 3` → Code attendu : `DP0003`

### ✅ Résultats Attendus
- ✅ Les codes sont générés dans l'**ordre séquentiel**
- ✅ Format : `DP0001`, `DP0002`, `DP0003`, etc.
- ✅ **Aucune collision** (pas de doublons)

---

## 🧪 TEST 7 : ÉDITION D'UN DÉPÔT

### Objectif
Valider que l'édition fonctionne sans problème.

### Étapes
1. Cliquer sur **"Modifier"** sur un dépôt existant
2. Changer le **nom** : `Nom Modifié`
3. Cliquer sur le **toggle** pour désactiver le dépôt
4. Cliquer sur **"Mettre à jour"**

### ✅ Résultats Attendus
- ✅ Les modifications sont **sauvegardées**
- ✅ Le badge passe de **"Actif"** (vert) à **"Inactif"** (rouge)
- ✅ Pas d'espace créé par le toggle

---

## 📋 CHECKLIST RAPIDE

Cochez chaque test après validation :

- [ ] ✅ Test 1 : Création sans code (auto-génération)
- [ ] ✅ Test 2 : Création avec code personnalisé
- [ ] ✅ Test 3 : Toggle sans espace (UX fix)
- [ ] ✅ Test 4 : Gestion des erreurs (modal reste ouvert)
- [ ] ✅ Test 5 : Loading state (feedback visuel)
- [ ] ✅ Test 6 : Séquence auto-génération
- [ ] ✅ Test 7 : Édition d'un dépôt

---

## 🐛 RAPPORTER UN BUG

Si vous trouvez un problème :

1. **Consulter les logs** :
   ```bash
   docker exec zenfleet_php tail -f storage/logs/laravel.log
   ```

2. **Vérifier les informations loguées** :
   - ✅ `Dépôt créé avec succès` : OK
   - ❌ `Erreur enregistrement dépôt` : Problème

3. **Informations à fournir** :
   - Navigateur utilisé (Chrome, Firefox, Safari)
   - Étape exacte qui pose problème
   - Message d'erreur visible
   - Stack trace dans les logs

---

## ✅ VALIDATION FINALE

### Critères de Succès
- ✅ Tous les tests passent sans erreur
- ✅ Aucun espace visuel avec le toggle
- ✅ Les codes sont auto-générés correctement
- ✅ Les erreurs sont visibles et le modal reste ouvert
- ✅ Les transitions sont fluides

### Prochaines Étapes
Si tous les tests passent :
1. ✅ Valider en **environnement de staging**
2. ✅ Former les utilisateurs sur l'auto-génération de code
3. ✅ Déployer en **production**

---

**Testeur** : _______________  
**Date** : _______________  
**Statut** : [ ] ✅ Validé  [ ] ❌ À revoir
