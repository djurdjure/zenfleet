# 🧪 Guide de Test : Affectation de Véhicules

## ✅ Correction Appliquée

**Problème résolu :** Erreur SQL `column "status" does not exist`  
**Fichiers modifiés :**
- `app/Http/Controllers/Admin/AssignmentController.php` (2 endroits)
- `resources/views/admin/assignments/create-enterprise.blade.php` (nouvelle interface)

---

## 🚀 Étapes de Test

### 1. Vider les Caches

```bash
cd /home/lynx/projects/zenfleet
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 2. Accéder à l'Interface

1. Connectez-vous à l'application ZenFleet
2. Naviguez vers **Affectations** → **Nouvelle Affectation**
3. URL : `http://your-app.com/admin/assignments/create`

### 3. Vérifier le Chargement

**✅ Attendu :**
- Page se charge sans erreur
- Compteurs de ressources affichés :
  - 🚗 Nombre de véhicules disponibles
  - 👤 Nombre de chauffeurs libres
- Dropdowns fonctionnels avec recherche

**❌ Si erreur :**
- Vérifier les logs : `tail -f storage/logs/laravel.log`
- Vérifier la connexion DB

### 4. Tester l'Affectation Ouverte

**Scénario :** Affectation sans date de fin

1. **Sélectionner un véhicule**
   - Utiliser la recherche Tom Select
   - Vérifier que le kilométrage se remplit automatiquement

2. **Sélectionner un chauffeur**
   - Utiliser la recherche Tom Select
   - Vérifier les informations affichées

3. **Remplir les dates**
   - Date de début : Aujourd'hui ou demain
   - Heure de début : Heure actuelle ou future
   - Kilométrage initial : Automatiquement rempli

4. **Type d'affectation**
   - Laisser "Ouverte" sélectionné (par défaut)
   - Vérifier que la section "Fin" est cachée

5. **Soumettre**
   - Cliquer sur "Créer l'Affectation"
   - **Attendu :** Redirection vers la liste avec message de succès

### 5. Tester l'Affectation Programmée

**Scénario :** Affectation avec date de fin

1. Répéter les étapes 1-3 ci-dessus

2. **Type d'affectation**
   - Cliquer sur "Programmée"
   - Vérifier que la section "Fin" apparaît avec animation

3. **Remplir la date de fin**
   - Date de fin : Après la date de début
   - Heure de fin : Après l'heure de début

4. **Soumettre**
   - Cliquer sur "Créer l'Affectation"
   - **Attendu :** Redirection vers la liste avec message de succès

### 6. Tester la Validation

**Scénario :** Champs manquants

1. Accéder au formulaire
2. Cliquer sur "Créer l'Affectation" sans remplir
3. **Attendu :** 
   - Messages d'erreur en rouge
   - Champs invalides bordés en rouge
   - Formulaire reste sur la page

### 7. Tester l'API des Chauffeurs Disponibles

```bash
# Requête API (avec authentification)
curl -H "Authorization: Bearer YOUR_TOKEN" \
     http://your-app.com/api/admin/assignments/available-drivers
```

**Attendu :**
```json
[
  {
    "id": 1,
    "full_name": "Jean Dupont",
    "first_name": "Jean",
    "last_name": "Dupont",
    "license_number": "ABC123456",
    "personal_phone": "0612345678",
    "status": "Actif",
    "status_color": "#10b981"
  }
]
```

---

## 🔍 Points de Vérification

### Base de Données

Vérifier que les données sont correctement enregistrées :

```sql
-- Dernière affectation créée
SELECT * FROM assignments 
ORDER BY created_at DESC 
LIMIT 1;

-- Vérifier le statut
SELECT 
    a.id,
    v.registration_plate,
    d.first_name,
    d.last_name,
    a.start_datetime,
    a.end_datetime,
    a.status
FROM assignments a
JOIN vehicles v ON a.vehicle_id = v.id
JOIN drivers d ON a.driver_id = d.id
ORDER BY a.created_at DESC
LIMIT 5;
```

### Logs Laravel

Surveiller les logs pendant les tests :

```bash
tail -f storage/logs/laravel.log
```

**Logs attendus :**
```
[INFO] 🚗 Tom Select Véhicules initialisé avec X options
[INFO] 👤 Tom Select Chauffeurs initialisé avec X options
[INFO] Nouvelle affectation créée
```

**Erreurs à surveiller :**
```
[ERROR] Erreur lors de la création de l'affectation
```

---

## 📊 Checklist de Validation

- [ ] Page de création charge sans erreur SQL
- [ ] Véhicules disponibles s'affichent correctement
- [ ] Chauffeurs disponibles s'affichent correctement
- [ ] Tom Select fonctionne (recherche, sélection)
- [ ] Kilométrage se remplit automatiquement
- [ ] Type d'affectation "Ouverte" fonctionne
- [ ] Type d'affectation "Programmée" fonctionne
- [ ] Section de fin apparaît/disparaît correctement
- [ ] Validation des champs requis fonctionne
- [ ] Soumission réussie créé l'affectation en DB
- [ ] Redirection vers la liste fonctionne
- [ ] Message de succès s'affiche
- [ ] API `/available-drivers` retourne les données
- [ ] Responsive mobile fonctionne
- [ ] Aucune erreur dans les logs

---

## ❌ Dépannage

### Problème 1 : Erreur SQL persiste

**Cause possible :** Ancien cache non vidé

**Solution :**
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

### Problème 2 : Dropdowns vides

**Cause possible :** Aucune ressource disponible

**Vérification :**
```sql
-- Véhicules avec statut actif
SELECT * FROM vehicles 
WHERE status_id IN (
    SELECT id FROM vehicle_statuses 
    WHERE is_active = true
);

-- Chauffeurs avec statut actif
SELECT * FROM drivers 
WHERE status_id IN (
    SELECT id FROM driver_statuses 
    WHERE is_active = true 
    AND can_drive = true 
    AND can_assign = true
);
```

**Solution :** Créer ou activer des ressources

### Problème 3 : Tom Select ne se charge pas

**Cause possible :** CDN bloqué ou JavaScript non chargé

**Vérification :**
1. Ouvrir la console du navigateur (F12)
2. Vérifier les erreurs JavaScript
3. Vérifier que Tom Select est chargé : `https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js`

**Solution :**
```bash
# Vérifier la connexion Internet
ping cdn.jsdelivr.net

# Alternative : Héberger Tom Select localement
```

### Problème 4 : Interface ne s'affiche pas

**Cause possible :** Vue non trouvée

**Vérification :**
```bash
ls -la resources/views/admin/assignments/create-enterprise.blade.php
```

**Solution :**
- Vérifier que le fichier existe
- Vérifier les permissions : `chmod 644 create-enterprise.blade.php`

---

## 📞 Support

Si les problèmes persistent :

1. **Vérifier les logs complets**
   ```bash
   cat storage/logs/laravel.log | grep -i "error"
   ```

2. **Activer le mode debug**
   ```env
   APP_DEBUG=true
   ```

3. **Vérifier la configuration DB**
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

4. **Contacter le support technique** avec :
   - Logs d'erreur
   - Screenshots de l'interface
   - Étapes pour reproduire

---

## ✅ Test Réussi

Si tous les points de la checklist sont validés, la correction est opérationnelle ! 🎉

**Prochaines étapes :**
1. Déployer en production
2. Former les utilisateurs
3. Monitorer les performances
