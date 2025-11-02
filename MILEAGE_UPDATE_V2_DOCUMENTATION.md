# Module de Mise à Jour du Kilométrage V2 - Documentation

> **Version:** 2.0 Enterprise Single Page  
> **Date:** 2025-11-02  
> **Type:** Composant Livewire 3 avec Design System ZenFleet

---

## 📋 Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture](#architecture)
3. [Fichiers créés](#fichiers-créés)
4. [Fonctionnalités](#fonctionnalités)
5. [Utilisation](#utilisation)
6. [Validation](#validation)
7. [Personnalisation](#personnalisation)
8. [Intégration](#intégration)

---

## 🎯 Vue d'ensemble

Le **Module de Mise à Jour du Kilométrage V2** est une refonte complète en page unique qui remplace l'ancienne implémentation. Il offre une expérience utilisateur optimale avec :

- ✅ **Interface moderne** : Design cohérent avec le Design System ZenFleet
- ✅ **Validation en temps réel** : Feedback immédiat sur la saisie
- ✅ **Recherche intelligente** : Tom Select pour trouver rapidement un véhicule
- ✅ **Historique contextuel** : 5 derniers relevés affichés
- ✅ **Statistiques** : Moyennes journalières/mensuelles calculées
- ✅ **UX fluide** : Animations, états de chargement, messages clairs

---

## 🏗️ Architecture

### Stack Technique

| Composant | Technologie | Rôle |
|-----------|-------------|------|
| **Backend** | Livewire 3 | Composant réactif principal |
| **Frontend** | Tailwind CSS + Alpine.js | Styling et micro-interactions |
| **Validation** | Laravel Validation Rules | Validation serveur et temps réel |
| **Composants** | Blade Components | Tom Select, Datepicker, TimePicker |
| **Layout** | `layouts.admin.catalyst` | Layout admin standardisé |

### Flux de Données

```
┌─────────────────┐
│   Utilisateur   │
└────────┬────────┘
         │ Sélectionne véhicule
         ▼
┌─────────────────────────────┐
│  MileageUpdateComponent     │
│  (Livewire)                 │
│  - Charge données véhicule  │
│  - Valide en temps réel     │
│  - Affiche statistiques     │
└────────┬────────────────────┘
         │ Soumet formulaire
         ▼
┌─────────────────────────────┐
│  save() Method              │
│  - Validation finale        │
│  - Transaction DB           │
│  - Création relevé          │
│  - MAJ vehicle.current_km   │
└────────┬────────────────────┘
         │
         ▼
┌─────────────────────────────┐
│  VehicleMileageReading      │
│  Model (via createManual()) │
└────────┬────────────────────┘
         │
         ▼
┌─────────────────────────────┐
│  Database                   │
│  + vehicle_mileage_readings │
│  + vehicles (current_km)    │
└─────────────────────────────┘
```

---

## 📁 Fichiers créés

### 1. Composant Livewire

**Fichier:** `app/Livewire/Admin/Mileage/MileageUpdateComponent.php`

**Responsabilités :**
- Gestion du formulaire et des propriétés
- Validation en temps réel du kilométrage
- Chargement des données véhicule
- Sauvegarde transactionnelle
- Calcul des statistiques

**Propriétés principales :**
```php
public ?int $vehicle_id;      // ID véhicule sélectionné
public string $date;           // Date relevé (Y-m-d)
public string $time;           // Heure relevé (H:i)
public ?int $mileage;          // Nouveau kilométrage
public ?string $notes;         // Notes optionnelles
public ?array $vehicleData;    // Données véhicule cached
```

**Méthodes principales :**
```php
mount(?int $vehicleId)         // Initialisation
updatedVehicleId($value)       // Événement changement véhicule
updatedMileage($value)         // Validation temps réel kilométrage
save()                         // Sauvegarde du relevé
resetForm()                    // Réinitialisation formulaire
```

**Propriétés calculées :**
```php
getAvailableVehiclesProperty() // Liste véhicules disponibles
getRecentReadingsProperty()    // 5 derniers relevés
getVehicleStatsProperty()      // Statistiques véhicule
```

### 2. Vue Blade principale

**Fichier:** `resources/views/livewire/admin/mileage/mileage-update-component.blade.php`

**Structure :**
- En-tête avec titre et lien historique
- Messages flash (succès/erreur)
- Layout 2 colonnes :
  - **Colonne principale (2/3)** : Formulaire de saisie
  - **Colonne latérale (1/3)** : Statistiques et historique

**Composants Blade utilisés :**
- `<x-tom-select>` : Recherche véhicule
- `<x-datepicker>` : Date de lecture
- `<x-time-picker>` : Heure de lecture
- `<x-input>` : Kilométrage
- `<x-textarea>` : Notes
- `<x-iconify>` : Icônes Lucide

### 3. Vue d'entrée

**Fichier:** `resources/views/admin/mileage-readings/update.blade.php`

**Contenu :**
```blade
@livewire('admin.mileage.mileage-update-component', ['vehicleId' => $vehicleId ?? null])
```

Simple wrapper qui charge le composant Livewire avec paramètre optionnel `vehicleId`.

---

## ⚡ Fonctionnalités

### 1. Sélection de véhicule intelligente

- **Tom Select** avec recherche en temps réel
- Affichage : `Immatriculation - Marque Modèle (Catégorie)`
- Filtrage automatique :
  - Véhicules non archivés
  - Statuts actifs uniquement
  - Respect des permissions utilisateur

### 2. Informations contextuelles

Une fois le véhicule sélectionné, affichage d'une carte bleue avec :
- Immatriculation (grand format)
- Marque, modèle, année
- Catégorie, carburant, dépôt
- **Kilométrage actuel en gras**

### 3. Validation en temps réel

Le champ "Nouveau kilométrage" affiche des messages dynamiques :

| Condition | Type | Message |
|-----------|------|---------|
| `km ≤ 0` | Error | "Le kilométrage doit être positif" |
| `km ≤ km_actuel` | Error | "Doit être supérieur à X km" |
| `km > km_actuel + 10000` | Warning | "⚠️ Augmentation importante : +X km" |
| `0 < différence ≤ 10000` | Success | "✓ Augmentation de X km" |

### 4. Date et heure

- **Datepicker Flatpickr** stylisé (calendrier français)
- **TimePicker** avec masque de saisie (format HH:MM)
- Contraintes :
  - Date ≤ aujourd'hui
  - Date ≥ aujourd'hui - 30 jours

### 5. Statistiques véhicule

Si le véhicule a ≥2 relevés, affichage :
- **Moyenne journalière** : km/jour sur les 30 derniers relevés
- **Moyenne mensuelle** : projection sur 30 jours
- **Km ce mois-ci** : total parcouru depuis le 1er du mois
- **Dernier relevé** : date et heure

### 6. Historique récent

Affichage des **5 derniers relevés** avec :
- Kilométrage (formaté avec espaces)
- Badge "Manuel" ou "Auto"
- Date/heure
- Nom de l'utilisateur enregistreur
- Notes (si présentes, limitées à 50 caractères)

### 7. Instructions intégrées

Carte d'aide bleue avec rappels :
- Kilométrage > dernier relevé
- Date limitée à 30 jours dans le passé
- Notes optionnelles mais recommandées
- Alerte si +10 000 km

---

## 🚀 Utilisation

### Accès

**URL :** `/admin/mileage-readings/update/{vehicle?}`

**Routes nommées :**
```php
route('admin.mileage-readings.update')           // Sélection manuelle
route('admin.mileage-readings.update', ['vehicle' => 42]) // Véhicule pré-sélectionné
```

**Permissions :**
- **Admin/Gestionnaire Flotte** : Tous les véhicules de l'organisation
- **Superviseur/Chef de Parc** : Véhicules de son dépôt
- **Chauffeur** : Son véhicule assigné uniquement

### Workflow utilisateur

1. **Sélectionner un véhicule** via Tom Select
2. La carte d'informations véhicule s'affiche
3. **Vérifier le kilométrage actuel** affiché
4. **Saisir le nouveau kilométrage**
   - Feedback immédiat (vert/jaune/rouge)
5. **Ajuster date/heure** si nécessaire (par défaut : maintenant)
6. **(Optionnel)** Ajouter des notes
7. **Cliquer "Enregistrer la Lecture"**
8. Message de succès détaillé s'affiche
9. Formulaire se réinitialise automatiquement

### Exemple de message de succès

```
✓ Kilométrage enregistré avec succès pour AB-123-CD : 
  125 000 km → 125 450 km (+450 km)
```

---

## ✅ Validation

### Règles serveur (Laravel)

| Champ | Règles | Message |
|-------|--------|---------|
| `vehicle_id` | `required|integer|exists:vehicles,id` | "Veuillez sélectionner un véhicule" |
| `date` | `required|date|before_or_equal:today|after_or_equal:-30days` | "Date ne peut dépasser 30 jours dans le passé" |
| `time` | `required|date_format:H:i` | "Format HH:MM requis" |
| `mileage` | `required|integer|min:0|max:9999999|gt:{current}` | "Doit être supérieur au dernier relevé" |
| `notes` | `nullable|string|max:500` | "Maximum 500 caractères" |

### Règle dynamique

La règle `gt:{current_mileage}` est ajoutée dynamiquement selon le dernier relevé du véhicule.

### Validation côté client

- Alpine.js gère l'affichage des erreurs
- Messages d'erreur avec icônes Lucide
- Animation fade-in pour les messages

---

## 🎨 Personnalisation

### Couleurs

Le module utilise la palette ZenFleet :
- **Primary** : `bg-primary-600` (bleu #0ea5e9)
- **Success** : `bg-green-50`, `text-green-800`
- **Warning** : `bg-yellow-50`, `text-yellow-800`
- **Error** : `bg-red-50`, `text-red-800`

### Icônes

Toutes les icônes proviennent de **Lucide Icons** via `<x-iconify>` :
- `lucide:gauge` : Kilométrage
- `lucide:car` : Véhicule
- `lucide:check-circle-2` : Succès
- `lucide:alert-circle` : Erreur
- `lucide:bar-chart-3` : Statistiques
- `lucide:history` : Historique

### Adapter le design

Pour modifier l'apparence :

1. **Changer les couleurs** : Remplacer les classes Tailwind dans la vue Blade
2. **Modifier les icônes** : Changer les attributs `icon="lucide:..."` des composants `<x-iconify>`
3. **Ajuster les colonnes** : Modifier `lg:col-span-2` / `lg:col-span-1` pour changer les proportions
4. **Personnaliser les composants** : Éditer les composants Blade dans `resources/views/components/`

---

## 🔗 Intégration

### Événements Livewire

Le composant émet un événement après sauvegarde :

```php
$this->dispatch('mileage-updated', vehicleId: $vehicleId);
```

**Écouter l'événement dans un autre composant Livewire :**

```php
#[On('mileage-updated')]
public function refreshData($vehicleId)
{
    // Rafraîchir vos données
    $this->loadVehicleReadings($vehicleId);
}
```

### Liens vers d'autres modules

**Dans la vue :**
```blade
<a href="{{ route('admin.mileage-readings.index') }}">
    Voir l'historique
</a>
```

**Depuis un autre module :**
```blade
<a href="{{ route('admin.mileage-readings.update', ['vehicle' => $vehicle->id]) }}">
    Mettre à jour le kilométrage
</a>
```

### Bouton d'action dans une table de véhicules

```blade
<a href="{{ route('admin.mileage-readings.update', ['vehicle' => $vehicle->id]) }}"
   class="inline-flex items-center gap-1 px-3 py-1 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">
    <x-iconify icon="lucide:gauge" class="w-3 h-3" />
    Mettre à jour KM
</a>
```

---

## 🧪 Tests

### Test manuel

1. **Accéder au module** : `/admin/mileage-readings/update`
2. **Sélectionner un véhicule** : Vérifier que la carte d'infos s'affiche
3. **Saisir kilométrage invalide** (≤ actuel) : Message d'erreur rouge doit apparaître
4. **Saisir kilométrage valide** : Message vert doit apparaître
5. **Soumettre le formulaire** : Message de succès + formulaire réinitialisé
6. **Vérifier l'historique** : Le nouveau relevé doit apparaître dans la colonne latérale
7. **Vérifier les stats** : Les moyennes doivent être recalculées

### Test avec paramètre URL

Tester l'URL avec véhicule pré-sélectionné :
```
/admin/mileage-readings/update/42
```
Le véhicule ID 42 doit être automatiquement sélectionné et ses infos affichées.

### Test des permissions

- **Admin** : Doit voir tous les véhicules
- **Superviseur** : Ne doit voir que les véhicules de son dépôt
- **Chauffeur** : Ne doit voir que son véhicule assigné

---

## 📊 Base de données

### Table `vehicle_mileage_readings`

Le composant crée un enregistrement via la méthode statique :

```php
VehicleMileageReading::createManual(
    organizationId: auth()->user()->organization_id,
    vehicleId: $this->vehicleData['id'],
    mileage: $this->mileage,
    recordedById: auth()->id(),
    recordedAt: Carbon::parse($this->date . ' ' . $this->time),
    notes: $this->notes
);
```

**Colonnes créées :**
- `organization_id` : ID organisation (multi-tenant)
- `vehicle_id` : ID véhicule
- `mileage` : Kilométrage saisi
- `recorded_at` : Date/heure combinée
- `recorded_by_id` : ID utilisateur
- `recording_method` : `'manual'`
- `notes` : Notes optionnelles

### Table `vehicles`

Le champ `current_mileage` est mis à jour :

```php
Vehicle::where('id', $this->vehicleData['id'])
    ->update(['current_mileage' => $this->mileage]);
```

---

## 🔧 Maintenance

### Dépendances externes

Le module utilise :
- **Tom Select 2.3.1** : Chargé via `<x-tom-select>` (CDN dans le composant)
- **Flatpickr** : Chargé via `<x-datepicker>` et `<x-time-picker>` (CDN dans les composants)
- **Alpine.js 3.13** : Déjà chargé dans le layout

Ces dépendances sont gérées par les composants Blade, aucune action requise.

### Logs

En cas d'erreur lors de la sauvegarde, un log est créé :

```php
\Log::error('Erreur enregistrement kilométrage', [
    'vehicle_id' => $this->vehicle_id,
    'mileage' => $this->mileage,
    'error' => $e->getMessage(),
    'trace' => $e->getTraceAsString()
]);
```

Consulter les logs : `storage/logs/laravel.log`

### Performance

**Optimisations appliquées :**
- Propriétés calculées Livewire (lazy loading)
- Requêtes avec `with()` pour éviter N+1
- Limitation à 5 relevés dans l'historique
- Limitation à 30 relevés pour les stats

---

## 📚 Ressources

### Fichiers de référence

- **Documentation technique** : `DOCUMENTATION_TECHNIQUE_COMPLETE.md`
- **Design System** : `DESIGN_SYSTEM.md`
- **Composants demo** : `/admin/components-demo`

### Standards du projet

- **PSR-12** pour le code PHP
- **Livewire 3** patterns et best practices
- **Tailwind CSS** avec classes utilitaires
- **Alpine.js** pour micro-interactions

### Support

Pour toute question ou amélioration :
1. Consulter les fichiers de documentation existants
2. Vérifier les composants Blade dans `resources/views/components/`
3. Examiner les autres modules similaires (Vehicles, Drivers, etc.)

---

## ✨ Améliorations futures possibles

### Court terme
- [ ] Export PDF du relevé après sauvegarde
- [ ] Notification email au gestionnaire après relevé
- [ ] Photo du compteur en pièce jointe

### Moyen terme
- [ ] Graphique d'évolution du kilométrage (Chart.js/ApexCharts)
- [ ] Alertes prédictives de maintenance selon kilométrage
- [ ] Import en masse via CSV

### Long terme
- [ ] Intégration GPS pour relevés automatiques
- [ ] Application mobile dédiée chauffeurs
- [ ] API RESTful pour intégrations tierces

---

*Document généré le 2025-11-02 - Version 2.0 Enterprise*  
*Module créé par Claude Code - Expert ZenFleet Architecture*
