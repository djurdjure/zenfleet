# Recommandation Graphiques ZenFleet

## 1) Resume executif

ZenFleet dispose deja d'une base solide pour les visualisations, mais l'implementation actuelle est heterogene:
- `apexcharts` est installe via package manager et optimise dans Vite
- plusieurs ecrans utilisent encore des scripts CDN (`Chart.js` / `ApexCharts`) dans des vues Blade

Ce melange cree des risques:
- versions non maitrisees
- comportement non uniforme entre pages
- difficultes de maintenance et de debugging
- cout de performance (scripts redondants)

Recommandation principale:
1. standardiser court terme sur `ApexCharts` (deja installe, deja bundle Vite)
2. retirer les CDN dans les vues
3. introduire une couche JS graphique unifiee pour Livewire

## 2) Etat reel de l'environnement (constate)

### Stack technique

- Laravel `12.28.1`
- PHP `8.3.25`
- PostgreSQL `18.0` + PostGIS
- Redis `7.4.5`
- Node `20.19.4`
- Vite `6.x`
- Tailwind `4.x`

### Bibliotheques de visualisation detectees

Installees via `package.json`:
- `apexcharts` `^4.2.0`

Utilisation detectee dans le code:
- import npm dans `resources/js/app.js` (exposition `window.ApexCharts`)
- chunk Vite dedie dans `vite.config.js` (`charts: ['apexcharts']`)
- references CDN Chart.js et/ou ApexCharts dans plusieurs vues Blade

## 3) Diagnostic technique

### Forces

- infrastructure moderne (Vite + split chunks)
- bibliotheque graphique deja presente et exploitable (`ApexCharts`)
- stack Livewire 3 adaptee a des dashboards reactifs

### Faiblesses

- absence de design system graphique unique (styles, palettes, interactions)
- coexistence npm + CDN
- risque de divergence entre modules (admin, analytics, maintenance)
- absence de contrat de donnees chart standardise

### Risques produit

- regression visuelle lors d'evolutions
- incoherence UX inter-modules
- difficulte de monitoring/perf (re-renders inutiles)
- augmentation de la dette technique

## 4) Recommandation de bibliotheques (niveau enterprise)

### Option A (recommandee immediatement): standardiser sur ApexCharts

Pourquoi:
- deja installe et partiellement integre
- API claire pour line/bar/area/radial/heatmap
- bonne ergonomie pour dashboards metier
- migration rapide sans rupture

Limites:
- moins puissant qu'ECharts sur certains cas ultra complexes

### Option B (cible analytique avancee): Apache ECharts

Pourquoi:
- tres large couverture de charts
- excellent pour scenarios analytiques avances
- forte capacite de personnalisation

Limites:
- integration plus lourde
- courbe d'apprentissage superieure

### Option C (non prioritaire ici): Chart.js

Pourquoi:
- simple et populaire

Limites:
- souvent moins riche pour dashboards enterprise complexes
- actuellement utilise en CDN dans ZenFleet, ce qui est a eviter

## 5) Decision cible proposee

### Horizon 0-3 mois

- conserver `ApexCharts` comme standard unique
- retirer les CDN graphiques des vues
- centraliser les configurations de themes et tooltips

### Horizon 3-6 mois

- evaluer `ECharts` seulement si besoin reel:
  - geospatial analytic avance
  - sankey / graph relations complexes
  - volumes data tres eleves avec interactions expertes

## 6) Architecture recommandee pour ZenFleet

Creer une couche unifiee:

```text
resources/js/charts/
  core/chart-registry.js
  core/chart-theme.js
  adapters/apex-adapter.js
  contracts/chart-payload-schema.js
  widgets/
    fleet-utilization.chart.js
    maintenance-cost.chart.js
    fuel-trend.chart.js
```

Principes:
- un seul point d'entree par type de widget
- payload JSON standard (labels, series, units, period, tenant_meta)
- destruction/recreation maitrisee sur hooks Livewire
- aucune initialisation chart inline dans Blade

## 7) Contrat de donnees chart (propose)

Chaque endpoint retourne un schema uniforme:

```json
{
  "meta": {
    "tenant_id": 2,
    "period": "last_30_days",
    "timezone": "Africa/Algiers",
    "currency": "DZD",
    "generated_at": "2026-02-09T10:15:00Z"
  },
  "labels": ["2026-01-10", "2026-01-11"],
  "series": [
    { "key": "fuel_cost", "name": "Cout carburant", "data": [12000, 9800] },
    { "key": "maintenance_cost", "name": "Cout maintenance", "data": [3000, 4100] }
  ]
}
```

Benefices:
- compatibilite inter-modules
- tests automatisables
- migration lib plus simple a terme

## 8) Integration Livewire sans regression

Pour eviter les erreurs DOM et re-inits instables:

- encapsuler les containers chart avec `wire:ignore`
- declencher les updates via events Livewire dedies
- detruire explicitement l'instance chart avant recreation
- bannir les scripts inline qui manipulent directement le DOM au render Blade

Exemple d'event:
- `dashboard:data-updated`
- payload minimal serialise

## 9) Performance et scalabilite multi-tenant

### Base de donnees

- pre-aggregation par jour/semaine/mois pour les indicateurs lourds
- indexes sur `(organization_id, date)` pour toutes tables source de KPI
- materialized views pour analytics frequentes

### Cache

- cache des series par cle composite:
  - `tenant`
  - `scope role`
  - `period`
  - `filters`
- TTL differencie:
  - temps reel court (1-5 min)
  - analytics historiques plus long (15-60 min)

### Frontend

- limiter le nombre de points par chart (downsampling)
- lazy-load des charts non visibles
- eviter chargement simultane de toutes cartes longues

## 10) Securite applicative

Exigences critiques:
- isolation stricte des donnees par `organization_id` cote serveur
- ne jamais filtrer tenant seulement cote client
- valider les filtres de periode/ressource (allow-list)
- journaliser les acces analytics sensibles (qui, quoi, quand)

## 11) Plan de migration propose

### Phase 1 - Stabilisation (1 sprint)

1. inventorier toutes vues avec scripts chart inline/CDN
2. remplacer par modules Vite centralises
3. normaliser palette + typography + interactions

### Phase 2 - Normalisation (1 sprint)

1. creer `chart-theme.js` et conventions communes
2. introduire contrat payload unique
3. ajouter tests feature/API sur endpoints analytics

### Phase 3 - Optimisation (1 sprint)

1. instrumenter temps de rendu charts
2. ajouter cache et pre-aggregation
3. surveiller charge DB/API par tenant

## 12) KPIs de succes

- 100% des charts servies sans CDN
- 100% des ecrans analytics via couche chart unifiee
- reduction du JS charge sur pages dashboard
- baisse des erreurs front liees aux re-renders DOM/Livewire
- temps median de rendu dashboard < 1.5s (hors cold start)

## 13) Recommandations concretes pour ZenFleet

Priorite haute:
1. converger sur `ApexCharts` immediatement
2. retirer les `script src` CDN des vues Blade
3. factoriser un module `resources/js/charts` commun

Priorite moyenne:
1. definir design tokens des charts (couleurs, grid, legendes, badges)
2. creer un composant Livewire/Blade standard pour chart containers
3. mettre en place tests de non-regression visuelle sur dashboards critiques

Priorite strategique:
1. evaluer `Apache ECharts` seulement pour besoins analytiques avances identifies
2. introduire un module "insights moteur" (anomalies, previsions, alertes)
3. preparer une couche geospatiale chart/map coherente avec PostGIS

## 14) Conclusion

ZenFleet a deja la fondation necessaire pour des dashboards de classe enterprise.  
La meilleure trajectoire sans regression est une convergence rapide vers une seule filiere graphique (`ApexCharts` via Vite), puis une industrialisation de la couche analytics (contrat de donnees, cache multi-tenant, governance UX).

---

## 15) Compléments d'expertise internationale

> **Note d'audit** : Les recommandations ci-dessous complètent le rapport initial pour atteindre un niveau enterprise-grade conforme aux standards Fleetio/Samsara.

### 15.1) Typage TypeScript pour la couche charts

Pour une maintenabilité long-terme, introduire des types stricts :

```typescript
// resources/js/charts/contracts/types.d.ts
interface ChartMeta {
  tenant_id: number;
  period: 'last_7_days' | 'last_30_days' | 'last_90_days' | 'custom';
  timezone: string;
  currency: string;
  generated_at: string;
}

interface ChartSeries {
  key: string;
  name: string;
  data: number[];
  unit?: 'currency' | 'percentage' | 'count' | 'distance';
}

interface ChartPayload {
  meta: ChartMeta;
  labels: string[];
  series: ChartSeries[];
}
```

**Bénéfice** : Détection d'erreurs au build, autocomplétion IDE, documentation implicite.

### 15.2) Accessibilité (WCAG 2.1 AA)

Les dashboards enterprise doivent respecter les standards d'accessibilité :

- **Contraste couleurs** : Ratio minimum 4.5:1 pour les légendes et labels
- **Navigation clavier** : Focus visible sur éléments interactifs des charts
- **Lecteurs d'écran** : Attributs `aria-label` sur les containers chart avec résumé textuel
- **Patterns alternatifs** : Utiliser texture/motif en plus des couleurs pour les daltoniens

```javascript
// Exemple ApexCharts + accessibilité (implémentation compatible)
const container = document.getElementById('fleet-chart');
container.setAttribute('role', 'img');
container.setAttribute(
  'aria-label',
  'Évolution des coûts flotte sur 30 jours, tendance en baisse de 8 pour cent'
);

const options = {
  chart: {
    toolbar: { show: true, tools: { download: true } }
  },
  fill: {
    pattern: { style: ['verticalLines', 'horizontalLines', 'squares'] }
  }
};

// Ajouter un résumé textuel hors chart pour lecteurs d'écran
// <p class="sr-only" id="fleet-chart-summary">Résumé KPI ...</p>
// container.setAttribute('aria-describedby', 'fleet-chart-summary');
```

### 15.3) Tests de non-régression visuelle

Au-delà des tests API, implémenter des **visual regression tests** :

- **Outil recommandé** : Percy (SaaS) ou Playwright avec `toHaveScreenshot()`
- **Couverture** : Capturer chaque widget chart sur données de référence
- **CI/CD** : Bloquer les merges si delta visuel > seuil configuré

```javascript
// tests/visual/dashboard-charts.spec.js
test('fleet-utilization chart matches snapshot', async ({ page }) => {
  await page.goto('/dashboard');
  await page.waitForSelector('[data-chart="fleet-utilization"]');
  await expect(page.locator('[data-chart="fleet-utilization"]'))
    .toHaveScreenshot('fleet-utilization.png');
});
```

### 15.4) Hydration Livewire et SSR

**Point critique** : Éviter le flash de contenu non stylisé (FOUC) sur les charts.

- Afficher un skeleton/placeholder pendant le chargement initial
- Utiliser `wire:init` pour déclencher le fetch de données après hydration
- Ne jamais `render()` de chart dans le HTML initial Blade

```blade
{{-- Composant Livewire chart --}}
<div wire:ignore x-data="chartWidget()" x-init="initChart()">
    <div wire:loading class="animate-pulse bg-gray-200 h-64 rounded"></div>
    <div wire:loading.remove id="chart-{{ $chartId }}"></div>
</div>
```

### 15.5) Export PDF haute fidélité avec le microservice Node

Le rapport mentionne la coexistence DOMPDF/microservice Node. **Recommandation claire** :

| Cas d'usage | Solution |
|-------------|----------|
| Exports simples (listes, factures) | DOMPDF (PHP) |
| Dashboards avec charts | Microservice Node (Puppeteer) |

Le microservice doit exposer :
- `POST /generate-pdf` avec payload HTML + options viewport (aligné avec la config actuelle)
- Capture via Puppeteer/Playwright headless
- Support du mode @print pour styles optimisés

### 15.6) Critères de migration vers ECharts

Ne migrer vers Apache ECharts que si **au moins 2 critères** sont validés :

1. **Volume data** : > 10 000 points par chart nécessitant WebGL
2. **Géospatial avancé** : Heatmaps/choroplèthes sur cartographie PostGIS
3. **Graphes relationnels** : Sankey, force-directed graphs pour analyse de flotte
4. **3D** : Visualisations tridimensionnelles requises

**Attention** : ECharts augmente le bundle de ~300KB (gzipped). Prévoir lazy-load module si adoption.

### 15.7) Monitoring et Observabilité

Compléter la stack avec du monitoring charts :

- **RUM (Real User Monitoring)** : Mesurer `chart.render.duration` côté client
- **Métriques** : Exposer vers Prometheus/Grafana
- **Alerting** : Seuil sur temps de rendu > 2s ou erreurs JS

```javascript
// Instrumentation ApexCharts via hooks natifs (compatible)
let chartStartTime = 0;

const options = {
  chart: {
    id: chartId,
    events: {
      beforeMount: () => {
        chartStartTime = performance.now();
      },
      mounted: (chartContext) => {
        const duration = performance.now() - chartStartTime;
        window.ZenFleet?.metrics?.track('chart_render', {
          chartId: chartContext?.opts?.chart?.id ?? chartId,
          duration,
          tenant: currentTenantId
        });
      },
      updated: (chartContext) => {
        window.ZenFleet?.metrics?.track('chart_updated', {
          chartId: chartContext?.opts?.chart?.id ?? chartId,
          tenant: currentTenantId
        });
      }
    }
  }
};
```

---

## 16) Validation finale

**Ce rapport atteint le niveau attendu pour une application enterprise multi-tenant.**

Les recommandations sont cohérentes avec :
- L'architecture Docker containerisée
- Le tuning PostgreSQL enterprise-grade
- La stack moderne Laravel 12 + Livewire 3 + Vite 6
- Les exigences multi-tenant (isolation, cache, sécurité)

✅ **Approuvé pour implémentation** - Suivre le plan de migration en 3 phases.
