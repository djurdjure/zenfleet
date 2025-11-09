# ⚡ Guide de Déploiement Rapide - Solution Enterprise

## 🎯 Vue d'Ensemble Ultra-Rapide

**Problèmes résolus :**
1. ✅ Bouton "Terminer" maintenant visible
2. ✅ Libération automatique véhicules/chauffeurs quand affectation se termine

**Fichiers modifiés :** 5 | **Fichiers créés :** 4 | **Documentation :** 8 fichiers

---

## 🚀 Déploiement en 5 Minutes

### Étape 1 : Vérifier les Prérequis (1 min)

```bash
# Redis actif ?
redis-cli ping
# Doit retourner : PONG

# Vérifier configuration queue dans .env
cat .env | grep QUEUE_CONNECTION
# Doit être : QUEUE_CONNECTION=redis
```

Si Redis pas installé :
```bash
sudo apt update
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

---

### Étape 2 : Vider les Caches (30 sec)

```bash
cd /var/www/zenfleet  # Adapter le chemin

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
php artisan cache:clear
```

---

### Étape 3 : Re-générer les Caches Optimisés (30 sec)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

### Étape 4 : Configurer Queue Workers avec Supervisor (2 min)

**Créer le fichier de configuration :**
```bash
sudo nano /etc/supervisor/conf.d/zenfleet-worker.conf
```

**Contenu :**
```ini
[program:zenfleet-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/zenfleet/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/zenfleet-worker.log
stopwaitsecs=3600
```

**Activer :**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start zenfleet-worker:*
```

**Vérifier :**
```bash
sudo supervisorctl status
# Doit afficher : zenfleet-worker:zenfleet-worker_00 RUNNING
#                 zenfleet-worker:zenfleet-worker_01 RUNNING
```

---

### Étape 5 : Configurer le Scheduler (1 min)

**Option A - Crontab (recommandé pour démarrage simple) :**
```bash
sudo crontab -e
```

**Ajouter cette ligne :**
```
* * * * * cd /var/www/zenfleet && php artisan schedule:run >> /dev/null 2>&1
```

**Option B - Systemd (recommandé pour production enterprise) :**
```bash
sudo nano /etc/systemd/system/zenfleet-scheduler.service
```

**Contenu :**
```ini
[Unit]
Description=ZenFleet Laravel Scheduler
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/zenfleet
ExecStart=/usr/bin/php /var/www/zenfleet/artisan schedule:work
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

**Activer :**
```bash
sudo systemctl daemon-reload
sudo systemctl enable zenfleet-scheduler
sudo systemctl start zenfleet-scheduler
sudo systemctl status zenfleet-scheduler
# Doit afficher : Active: active (running)
```

---

## ✅ Validation du Déploiement (2 min)

### Test #1 : Queue Workers Actifs

```bash
# Vérifier que les workers tournent
ps aux | grep "queue:work"

# Tester avec un job de test
php artisan tinker
> dispatch(function() { \Log::info('Test queue OK'); });
> exit

# Vérifier logs (après 5 secondes)
tail -f storage/logs/laravel.log | grep "Test queue OK"
# Doit afficher : "Test queue OK"
```

---

### Test #2 : Scheduler Actif

```bash
# Vérifier que le scheduler tourne (crontab ou systemd)

# Option crontab :
crontab -l | grep schedule:run

# Option systemd :
systemctl status zenfleet-scheduler

# Tester manuellement la commande
php artisan assignments:process-expired --dry-run

# Doit afficher :
# 🚀 Démarrage du traitement des affectations expirées...
# Mode: 🧪 DRY-RUN (simulation)
# ✅ Aucune affectation expirée à traiter. (ou X affectations trouvées)
```

---

### Test #3 : Bouton "Terminer" Visible

**Via navigateur :**
1. Accéder à `http://localhost/admin/assignments` (adapter l'URL)
2. Chercher une affectation avec badge "Active" (vert)
3. Vérifier présence du **bouton flag orange** 🏁 dans la colonne Actions

**Résultat attendu :** ✅ Bouton visible et cliquable

---

### Test #4 : Libération Automatique Fonctionne

**Via Tinker :**
```bash
php artisan tinker

# Créer une affectation de test
$vehicle = \App\Models\Vehicle::first();
$driver = \App\Models\Driver::first();

$assignment = \App\Models\Assignment::create([
    'vehicle_id' => $vehicle->id,
    'driver_id' => $driver->id,
    'start_datetime' => now()->subHours(2),
    'end_datetime' => null,
    'organization_id' => auth()->user()->organization_id ?? 1,
    'reason' => 'TEST DÉPLOIEMENT'
]);

# Vérifier statut AVANT
echo "Véhicule status_id AVANT : " . $vehicle->status_id . "\n";
echo "Chauffeur status_id AVANT : " . $driver->status_id . "\n";

# Terminer l'affectation
$assignment->end();

# Attendre 10 secondes (traitement queue async)
sleep(10);

# Vérifier statut APRÈS
$vehicle->refresh();
$driver->refresh();
echo "Véhicule status_id APRÈS : " . $vehicle->status_id . "\n";
echo "Chauffeur status_id APRÈS : " . $driver->status_id . "\n";

# Nettoyer
$assignment->delete();
```

**Résultat attendu :**
```
Véhicule status_id AVANT : 2 (En service)
Chauffeur status_id AVANT : 2 (En service)
Véhicule status_id APRÈS : 1 (Disponible)  ← ✅ LIBÉRÉ
Chauffeur status_id APRÈS : 1 (Disponible) ← ✅ LIBÉRÉ
```

---

## 📊 Monitoring Post-Déploiement

### Logs à Surveiller (temps réel)

```bash
# Logs généraux
tail -f storage/logs/laravel.log

# Logs spécifiques au système de libération
tail -f storage/logs/laravel.log | grep -E "ReleaseVehicleAndDriver|ProcessExpiredAssignments|AssignmentEnded"

# Logs workers queue
tail -f /var/log/zenfleet-worker.log

# Logs scheduler (si systemd)
journalctl -u zenfleet-scheduler -f
```

---

### Métriques à Vérifier (première heure)

**1. Nombre d'affectations terminées :**
```bash
grep "AssignmentEnded" storage/logs/laravel.log | wc -l
```

**2. Nombre de ressources libérées :**
```bash
grep "Véhicule libéré" storage/logs/laravel.log | wc -l
grep "Chauffeur libéré" storage/logs/laravel.log | wc -l
```

**3. Erreurs éventuelles :**
```bash
grep "ERROR" storage/logs/laravel.log | grep -E "ReleaseVehicleAndDriver|ProcessExpiredAssignments"
```

---

## 🔧 Dépannage Rapide

### Problème : Queue Workers Ne Démarrent Pas

**Diagnostic :**
```bash
sudo supervisorctl status
# Si FATAL ou BACKOFF :
sudo tail -f /var/log/zenfleet-worker.log
```

**Solutions :**
```bash
# Vérifier permissions
sudo chown -R www-data:www-data /var/www/zenfleet/storage

# Redémarrer
sudo supervisorctl restart zenfleet-worker:*
```

---

### Problème : Scheduler Ne S'Exécute Pas

**Diagnostic :**
```bash
# Vérifier crontab
crontab -l

# Ou vérifier systemd
systemctl status zenfleet-scheduler
journalctl -u zenfleet-scheduler -n 50
```

**Solutions :**
```bash
# Si crontab :
sudo crontab -e
# Vérifier que la ligne existe et que le chemin est correct

# Si systemd :
sudo systemctl restart zenfleet-scheduler
sudo systemctl status zenfleet-scheduler
```

---

### Problème : Bouton Toujours Invisible

**Diagnostic :**
```bash
php artisan tinker
$assignment = \App\Models\Assignment::first();
$assignment->canBeEnded();  // Doit retourner TRUE pour affectations actives

# Débugger :
$assignment->start_datetime;  // Dans le passé ?
$assignment->end_datetime;    // NULL ?
$assignment->getStatusAttribute($assignment->attributes['status'] ?? null);  // 'active' ?
```

**Solution :**
```bash
# Vider le cache des vues
php artisan view:clear
# Recharger la page avec CTRL + F5
```

---

## 📚 Documentation Complète

**Documentation technique approfondie :**
- `docs/SYSTEM_AUTO_RELEASE_RESOURCES.md` - Architecture complète + tests détaillés

**Correctifs antérieurs :**
- `docs/INDEX_CORRECTIFS_2025-11-09.md` - Index de toute la documentation
- `docs/GUIDE_TEST_RAPIDE_BOUTON_TERMINER.md` - Tests rapides (13 min)

---

## ✅ Checklist Finale

**Infrastructure :**
- [ ] Redis actif (`redis-cli ping` → PONG)
- [ ] Queue workers actifs (`sudo supervisorctl status`)
- [ ] Scheduler actif (`crontab -l` ou `systemctl status zenfleet-scheduler`)
- [ ] Caches Laravel vidés puis reconstruits

**Validation Fonctionnelle :**
- [ ] Test #1 : Queue workers traitent les jobs
- [ ] Test #2 : Scheduler exécute la commande
- [ ] Test #3 : Bouton "Terminer" visible
- [ ] Test #4 : Libération automatique fonctionne

**Monitoring :**
- [ ] Logs Laravel accessibles
- [ ] Logs workers accessibles
- [ ] Métriques initiales collectées (première heure)

---

## 🆘 Support

**En cas de problème :**

1. **Consulter les logs :**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Vérifier la documentation :**
   - `docs/SYSTEM_AUTO_RELEASE_RESOURCES.md` (section Dépannage)

3. **Tester manuellement :**
   ```bash
   php artisan assignments:process-expired --dry-run
   php artisan queue:work redis --once
   ```

---

**✅ DÉPLOIEMENT TERMINÉ**

**Temps total :** ~10 minutes (avec installation Redis si nécessaire)
**Prochaines étapes :** Monitoring des métriques (24h) puis mise en production définitive

---

**Date :** 2025-11-09
**Version :** 1.0-Enterprise
**Stack :** Laravel 12.0 + PostgreSQL 18 + Redis 7.x + Supervisor
