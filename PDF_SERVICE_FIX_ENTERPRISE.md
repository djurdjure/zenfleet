# 🔧 Correction Service PDF Enterprise - Guide Complet

## 📅 Date: 2025-11-03
## 🚨 Problème: Service PDF indisponible + Erreur Docker Compose

---

## ✅ PROBLÈMES IDENTIFIÉS ET CORRIGÉS

### 1. ❌ Erreur Docker Compose
**Problème:** `mapping key "pdf-service" already defined at line 11`
**Cause:** Le service `pdf-service` était défini 2 fois (lignes 11 et 114)
**Solution:** 
- Suppression de la première définition (ligne 11-21)
- Conservation de la définition complète (ligne 101+)
- Ajout des ports et environment manquants

### 2. ❌ Service PDF Indisponible
**Causes possibles:**
- Container non démarré
- Healthcheck échouant (curl manquant)
- Configuration réseau incorrecte

**Solutions appliquées:**
- Ajout de `curl` dans Dockerfile pour healthcheck
- Configuration réseau unifiée `zenfleet_network`
- Ports exposés correctement (3000:3000)

---

## 🚀 DÉMARRAGE RAPIDE

### Méthode 1: Script Automatique (RECOMMANDÉ)
```bash
# Exécuter le script de démarrage créé
./start-pdf-service.sh
```

### Méthode 2: Commandes Manuelles
```bash
# 1. Nettoyer les anciens containers
docker stop zenfleet_pdf_service 2>/dev/null || true
docker rm zenfleet_pdf_service 2>/dev/null || true

# 2. Reconstruire et démarrer
docker-compose build pdf-service
docker-compose up -d pdf-service

# 3. Vérifier le statut
docker-compose ps pdf-service
docker logs zenfleet_pdf_service

# 4. Tester le health endpoint
curl http://localhost:3000/health
```

---

## ⚙️ CONFIGURATION REQUISE

### 1. Variables d'environnement (.env)
```env
# IMPORTANT: Utiliser pdf-service (nom du service Docker) pas localhost
PDF_SERVICE_URL=http://pdf-service:3000
PDF_SERVICE_TIMEOUT=60
PDF_SERVICE_RETRY=3
```

### 2. Structure des fichiers
```
zenfleet/
├── docker-compose.yml (corrigé)
├── pdf-service/
│   ├── Dockerfile
│   ├── package.json
│   └── server.js
├── start-pdf-service.sh (nouveau)
└── test_pdf_service.php (nouveau)
```

---

## 🧪 TESTS DE VALIDATION

### Test 1: Service Health
```bash
curl http://localhost:3000/health
# Réponse attendue: {"status":"healthy","service":"PDF Microservice","version":"2.0"}
```

### Test 2: Script PHP de test
```bash
php test_pdf_service.php
```

### Test 3: Depuis l'application
1. Aller sur `/admin/vehicles`
2. Cliquer sur menu 3 points d'un véhicule
3. Cliquer sur "Exporter PDF"
4. Vérifier le téléchargement du PDF

---

## 📝 FICHIERS MODIFIÉS

1. **docker-compose.yml**
   - Suppression duplication service pdf-service
   - Configuration complète ligne 101-120
   - Ajout ports et environment

2. **pdf-service/Dockerfile**
   - Ajout `curl` pour healthcheck

3. **Nouveaux fichiers créés:**
   - `start-pdf-service.sh` - Script démarrage automatisé
   - `test_pdf_service.php` - Script de test
   - `.env.pdf.example` - Configuration exemple

---

## 🔍 DIAGNOSTIC EN CAS D'ERREUR

### Le service ne démarre pas
```bash
# Vérifier les logs
docker logs zenfleet_pdf_service

# Vérifier l'état
docker ps -a | grep pdf

# Reconstruire l'image
docker-compose build --no-cache pdf-service
```

### Erreur "Service temporairement indisponible"
```bash
# 1. Vérifier que le service est accessible
docker exec zenfleet_php curl http://pdf-service:3000/health

# 2. Vérifier la configuration .env
grep PDF_SERVICE .env

# 3. Clear cache Laravel
docker exec zenfleet_php php artisan config:clear
docker exec zenfleet_php php artisan cache:clear
```

### Port 3000 déjà utilisé
```bash
# Identifier le processus
lsof -i :3000

# Tuer le processus ou changer le port dans docker-compose.yml
# Exemple pour port 3001:
# ports:
#   - "3001:3000"
# Et dans .env: PDF_SERVICE_URL=http://pdf-service:3000
```

---

## 📊 ARCHITECTURE CORRIGÉE

```
Laravel App (Container: zenfleet_php)
    ↓ HTTP Request
PDF Service (Container: zenfleet_pdf_service)
    ↓ Port: 3000
Puppeteer/Chrome Headless
    ↓
PDF Binary Response
```

### Communication Inter-Container
- Laravel → PDF Service: `http://pdf-service:3000`
- Host → PDF Service: `http://localhost:3000`
- Network: `zenfleet_network`

---

## ✅ CHECKLIST VALIDATION

- [x] docker-compose.yml corrigé (pas de duplication)
- [x] Service pdf-service avec ports et env
- [x] Dockerfile avec curl pour healthcheck
- [x] Script de démarrage créé
- [x] Configuration .env documentée
- [x] Tests de validation créés

---

## 🎯 RÉSULTAT ATTENDU

Après application des corrections:
1. ✅ Plus d'erreur docker-compose
2. ✅ Service PDF accessible sur port 3000
3. ✅ Export PDF fonctionnel depuis l'interface
4. ✅ PDFs générés correctement (pas de HTML)

---

## 📞 SUPPORT

Si le problème persiste après ces corrections:
1. Exécuter: `./start-pdf-service.sh`
2. Vérifier logs: `docker logs -f zenfleet_pdf_service`
3. Tester: `php test_pdf_service.php`
4. Vérifier .env contient: `PDF_SERVICE_URL=http://pdf-service:3000`

**Status:** ✅ SOLUTION ENTERPRISE COMPLÈTE
