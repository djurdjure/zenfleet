# 🎨 GUIDE D'ACCÈS - Page Démonstration Composants ZenFleet

**Date:** 17 Octobre 2025
**Page:** Design System Components Demo
**Fichier:** `resources/views/admin/components-demo.blade.php`

---

## 🚀 ACCÈS RAPIDE

### URL d'Accès
```
http://votre-domaine.com/admin/components-demo
```

**OU avec Docker local:**
```
http://localhost/admin/components-demo
http://127.0.0.1/admin/components-demo
```

---

## 🔐 PRÉREQUIS

### 1. Authentification Requise
Vous devez être **connecté** avec un compte utilisateur.

### 2. Rôle Requis
Votre compte doit avoir l'un des rôles suivants :
- ✅ **Super Admin**
- ✅ **Admin**

**Note:** Les autres rôles (Gestionnaire Flotte, Supervisor, Chauffeur) n'ont **PAS accès** à cette page.

---

## 📍 ROUTE LARAVEL

### Configuration Route
```php
// Fichier: routes/web.php (ligne 112-114)

Route::get('/components-demo', function () {
    return view('admin.components-demo');
})
->name('components.demo')
->middleware('role:Super Admin|Admin');
```

### Nom de Route
```php
route('components.demo')
```

---

## 🎨 CONTENU DE LA PAGE

La page **components-demo.blade.php** affiche :

### 1. Design System ZenFleet
```
🎨 ZenFleet Design System
Composants Tailwind CSS utility-first réutilisables
```

### 2. Composants Affichés

**Buttons (Boutons):**
- Variantes: Primary, Secondary, Danger, Success, Warning, Info
- Tailles: Small, Medium, Large
- États: Normal, Disabled, Loading
- Avec icônes

**Forms (Formulaires):**
- Inputs text, email, password
- Textareas
- Selects
- Checkboxes & Radio buttons
- File uploads
- États de validation (success, error)

**Cards (Cartes):**
- Cards simples
- Cards avec header/footer
- Cards avec images
- Cards interactives

**Alerts (Alertes):**
- Success, Info, Warning, Error
- Avec/sans icônes
- Dismissible

**Badges (Badges):**
- Couleurs: Primary, Success, Warning, Danger
- Tailles variées
- Avec icônes

**Tables (Tableaux):**
- Tables simples
- Tables avec tri
- Tables avec pagination
- Tables responsive

**Modals (Modales):**
- Modales simples
- Modales avec formulaires
- Modales de confirmation

**Navigation:**
- Tabs (Onglets)
- Breadcrumbs (Fil d'Ariane)
- Pagination

**Loaders:**
- Spinners
- Progress bars
- Skeleton loaders

---

## 🛠️ MÉTHODES D'ACCÈS

### Méthode 1: URL Directe (Recommandée)
```
1. Connectez-vous à votre application
2. Dans la barre d'adresse, tapez:
   http://votre-domaine.com/admin/components-demo
3. Appuyez sur Entrée
```

### Méthode 2: Via Route Helper (Code PHP)
```php
// Dans un contrôleur ou une vue
return redirect()->route('components.demo');

// OU dans Blade
<a href="{{ route('components.demo') }}">Design System</a>
```

### Méthode 3: Ajout au Menu Latéral

**Optionnel:** Ajouter un lien dans le menu latéral Catalyst.

Éditez: `resources/views/layouts/admin/catalyst.blade.php`

```blade
{{-- Ajoutez ceci dans la section Configuration ou ailleurs --}}
@hasanyrole('Super Admin|Admin')
<li class="flex">
    <a href="{{ route('components.demo') }}"
       class="flex items-center w-full h-10 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('components.demo') ? 'bg-blue-50 text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-white/60 hover:text-slate-800' }}">
        <x-heroicon-o-swatch class="w-4 h-4 mr-3 {{ request()->routeIs('components.demo') ? 'text-blue-600' : 'text-slate-500' }}" />
        <span class="flex-1">Design System</span>
    </a>
</li>
@endhasanyrole
```

---

## 🧪 TESTS D'ACCÈS

### Test 1: Vérifier la Route
```bash
docker compose exec -u zenfleet_user php php artisan route:list | grep components
```

**Sortie attendue:**
```
GET|HEAD  admin/components-demo  components.demo  web
                                                  admin
                                                  auth
                                                  role:Super Admin|Admin
```

### Test 2: Accès Navigateur
```
1. Ouvrez votre navigateur
2. Connectez-vous avec un compte Admin ou Super Admin
3. Accédez à: http://localhost/admin/components-demo
4. Vérifiez que la page s'affiche sans erreur 403
```

### Test 3: Vérification Permissions
```bash
# Si vous obtenez une erreur 403 (Forbidden)
# Vérifiez votre rôle utilisateur:

docker compose exec -u zenfleet_user php php artisan tinker

# Dans Tinker:
>>> $user = App\Models\User::find(1); // Remplacez 1 par votre ID
>>> $user->getRoleNames();
# Devrait afficher: ["Super Admin"] ou ["Admin"]

# Si votre rôle n'est pas correct:
>>> $user->assignRole('Admin');
>>> exit
```

---

## 🔧 CONFIGURATION DOCKER

### Vérifier le Serveur Web
```bash
# Vérifier que Nginx/Apache est en cours d'exécution
docker compose ps

# Devrait afficher:
# NAME        SERVICE   STATUS
# zenfleet-php       php         running
# zenfleet-nginx     nginx       running
# zenfleet-postgres  postgres    running
```

### Vérifier les Logs
```bash
# Logs PHP-FPM
docker compose logs -f php

# Logs Nginx
docker compose logs -f nginx
```

### Redémarrer si Nécessaire
```bash
docker compose restart php nginx
```

---

## 📱 ACCÈS DEPUIS DIFFÉRENTS ENVIRONNEMENTS

### Local (Docker)
```
http://localhost/admin/components-demo
http://127.0.0.1/admin/components-demo
```

### Docker avec Port Custom
Si votre docker-compose.yml utilise un port différent (ex: 8080):
```
http://localhost:8080/admin/components-demo
```

### Développement avec php artisan serve
```bash
php artisan serve
# Puis accédez à:
http://localhost:8000/admin/components-demo
```

### Production
```
https://votre-domaine.com/admin/components-demo
```

---

## 🐛 TROUBLESHOOTING

### Erreur 404 (Not Found)

**Cause:** Route non trouvée

**Solution:**
```bash
# Clear le cache des routes
docker compose exec -u zenfleet_user php php artisan route:clear
docker compose exec -u zenfleet_user php php artisan config:clear
docker compose exec -u zenfleet_user php php artisan optimize:clear
```

### Erreur 403 (Forbidden)

**Cause:** Vous n'avez pas le bon rôle

**Solution:**
```bash
# Vérifiez votre rôle
docker compose exec -u zenfleet_user php php artisan tinker

>>> auth()->user()->getRoleNames()
# Si pas Admin/Super Admin:
>>> auth()->user()->assignRole('Admin')
```

### Erreur 500 (Server Error)

**Cause:** Erreur PHP dans la vue

**Solution:**
```bash
# Vérifier les logs
docker compose logs -f php

# Clear les caches
docker compose exec -u zenfleet_user php php artisan view:clear
docker compose exec -u zenfleet_user php php artisan optimize:clear
```

### Page Blanche

**Cause:** Erreur JavaScript ou CSS

**Solution:**
```bash
# Rebuild les assets
npm run build

# Clear le cache navigateur
Ctrl + Shift + R (Windows/Linux)
Cmd + Shift + R (Mac)
```

---

## 📊 INFORMATIONS TECHNIQUES

### Fichier Vue
```
Location: resources/views/admin/components-demo.blade.php
Layout:   layouts.admin.catalyst
Section:  content
```

### Route
```
Method:     GET
URI:        /admin/components-demo
Name:       components.demo
Middleware: web, admin, auth, role:Super Admin|Admin
```

### Permissions
```
Roles autorisés:
  - Super Admin ✅
  - Admin ✅

Roles refusés:
  - Gestionnaire Flotte ❌
  - Supervisor ❌
  - Chauffeur ❌
```

---

## 🎯 UTILISATION

### Pour les Développeurs
Cette page vous permet de :
- ✅ Visualiser tous les composants disponibles
- ✅ Tester les variantes de styles
- ✅ Copier les exemples de code
- ✅ Valider le Design System
- ✅ S'assurer de la cohérence UI/UX

### Pour les Designers
Cette page vous permet de :
- ✅ Valider les couleurs et typographies
- ✅ Vérifier les espacements et alignements
- ✅ Tester la responsive design
- ✅ Valider l'accessibilité
- ✅ Documenter les standards UI

---

## 📚 DOCUMENTATION COMPLÉMENTAIRE

### Design System
Consultez ces fichiers pour plus d'infos :
- `DESIGN_SYSTEM.md` - Documentation complète
- `resources/css/admin/app.css` - Styles Tailwind
- `resources/views/components/` - Composants Blade

### Composants Blade
```
resources/views/components/
├── button.blade.php
├── input.blade.php
├── card.blade.php
├── alert.blade.php
└── ...
```

---

## ✅ CHECKLIST D'ACCÈS

### Prérequis
- [ ] Docker containers en cours d'exécution
- [ ] Application Laravel accessible
- [ ] Compte utilisateur créé
- [ ] Rôle Admin ou Super Admin assigné
- [ ] Authentification réussie

### Accès
- [ ] URL testée: http://localhost/admin/components-demo
- [ ] Page s'affiche sans erreur
- [ ] Tous les composants sont visibles
- [ ] Aucune erreur JavaScript dans la console

---

## 🎨 EXEMPLE DE CONTENU

Voici ce que vous verrez sur la page :

```
┌────────────────────────────────────────────────────────┐
│  🎨 ZenFleet Design System                             │
│  Composants Tailwind CSS utility-first réutilisables   │
└────────────────────────────────────────────────────────┘

┌─ Buttons ──────────────────────────────────────────────┐
│                                                        │
│  Variantes:                                            │
│  [Primary] [Secondary] [Danger] [Success] ...          │
│                                                        │
│  Tailles:                                              │
│  [Small] [Medium] [Large]                              │
│                                                        │
│  États:                                                │
│  [Normal] [Disabled] [Loading]                         │
│                                                        │
└────────────────────────────────────────────────────────┘

┌─ Forms ────────────────────────────────────────────────┐
│  [_______________] Input                               │
│  [_______________] Email                               │
│  [_______________] Password                            │
│  [▼] Select                                            │
│  [✓] Checkbox   ○ Radio                                │
└────────────────────────────────────────────────────────┘

... et bien plus !
```

---

## 🚀 COMMANDE RAPIDE

**Tout-en-un pour accéder à la page:**

```bash
# 1. Vérifier que Docker tourne
docker compose ps

# 2. Clear les caches
docker compose exec -u zenfleet_user php bash -c "
  php artisan route:clear &&
  php artisan config:clear &&
  php artisan view:clear
"

# 3. Ouvrir dans le navigateur
# Linux/WSL:
xdg-open http://localhost/admin/components-demo

# macOS:
open http://localhost/admin/components-demo

# Windows:
start http://localhost/admin/components-demo
```

---

## 📞 SUPPORT

### Si Vous Ne Pouvez Toujours Pas Accéder

**Partagez-moi les informations suivantes:**

1. **Votre rôle utilisateur:**
```bash
docker compose exec -u zenfleet_user php php artisan tinker
>>> auth()->user()->getRoleNames()
```

2. **Status de la route:**
```bash
docker compose exec -u zenfleet_user php php artisan route:list | grep components
```

3. **Logs d'erreur:**
```bash
docker compose logs -f php | grep -i error
```

4. **URL testée:**
```
http://localhost/admin/components-demo
```

---

## 🏆 CONCLUSION

**Accès à la page Design System:**

```
URL: http://localhost/admin/components-demo
Route: components.demo
Rôles: Super Admin | Admin
Status: ✅ Disponible
```

**Commande rapide:**
```bash
# Ouvrir dans le navigateur après authentification
http://localhost/admin/components-demo
```

---

**🎨 Profitez de votre Design System ZenFleet !**

**Generated with Claude Code**
https://claude.com/claude-code
