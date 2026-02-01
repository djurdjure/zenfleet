# Audit Sécurité – ZenFleet (Multi‑tenant Fleet Management)

**Version du document**: 1.0  
**Date**: 2026‑02‑01  
**Auteur**: Audit sécurité applicatif (niveau expert international)  
**Périmètre**: Application Laravel 12 + Livewire 3 + PostgreSQL 18 (multi‑tenant) + Docker  
**Objectif**: Identifier les lacunes, erreurs de conception et de programmation impactant la sécurité, puis proposer un plan d’amélioration entreprise‑grade.

---

## 1) Résumé exécutif (haut niveau)

ZenFleet dispose de fondations solides pour le multi‑tenant (Spatie Permission avec teams, tenant resolver, RLS PostgreSQL). Cependant, des incohérences structurelles dans la **stratégie d’autorisation**, le **nommage des permissions**, et l’**usage dispersé des rôles hard‑codés** introduisent un risque réel d’accès non cohérent (403 inattendus, contournements involontaires, exposition de données inter‑organisation).  
L’absence d’un **modèle d’autorisation unifié** (single source of truth) est le principal facteur de dette sécurité.

Priorités absolues:
1) **Standardiser le modèle d’autorisation** (permissions, policies, middleware, scopes).  
2) **Réduire les rôles hard‑codés** et normaliser les libellés.  
3) **Garantir le scoping d’organisation** à 100% sur tous les modules sensibles.  
4) **Sécuriser les flux d’import/export** et les opérations à fort impact.  

---

## 2) Méthodologie

- Revue structurelle: routes, middlewares, policies, services, contrôleurs, Livewire.  
- Inspection des mécanismes multi‑tenant: Spatie Teams, resolver, RLS PostgreSQL.  
- Identification des contrôles d’accès: `can()`, `hasRole()`, `Gate`, policies, middlewares.  
- Détection des divergences et hard‑codages.  

**Limites**: audit basé sur inspection statique du code et conventions vues dans le projet.  
Les conclusions “à confirmer” sont listées explicitement dans la section “Plan de validation”.

---

## 3) Lacunes & erreurs (détaillées)

### 3.1 Gouvernance des permissions (critique)
**Constat**
- Usage **mixte** de permissions avec espaces (`view drivers`) et **dot‑notation** (`assignments.view`).  
- Alias partiels dans `EnterprisePermissionMiddleware` → couverture incomplète.  
- Certaines routes utilisent des permissions non standardisées (ex: `assignments.*` vs `view assignments`).  

**Impact**
- Autorisations incohérentes, erreurs 403 aléatoires, failles d’accès indirectes.  

**Exemples**
- `EnterprisePermissionMiddleware` mappe `admin.assignments.*` vers `assignments.view` (dot), alors que policies utilisent `view assignments`.  
- Certaines règles sont codées en dur (ex: `assignments.view` + `assignments.create`) et d’autres via `can('view assignments')`.  

**Risque**: Élevé (authz non déterministe).

---

### 3.2 Rôles hard‑codés et variations de nomenclature (critique)
**Constat**
- Plusieurs variantes des mêmes rôles:  
  - `Supervisor` / `Superviseur`  
  - `Fleet Manager` / `Gestionnaire Flotte`  
  - `Chef de Parc` / `Chef de parc`  
  - `Manager`, `Finance`, `Driver`  
- Ces libellés apparaissent dans services, policies, Livewire, middleware.

**Impact**
- La sécurité dépend du libellé exact en DB. Une variation casse la logique.  
- Difficulté majeure à gouverner l’autorisation à grande échelle.  

**Risque**: Élevé (fragmentation du modèle RBAC).

---

### 3.3 Double (ou triple) logique d’autorisation (élevé)
**Constat**
- Les contrôles sont répartis entre:  
  - `EnterprisePermissionMiddleware`  
  - Policies (ex: `DriverPolicy`, `AssignmentPolicy`)  
  - Checks inline (`hasRole`, `can`) dans Services/Controllers/Livewire

**Impact**
- Divergences d’accès entre UI/Routes/API.  
- “Bypass involontaire” ou “blocage faux‑positif”.

**Risque**: Élevé (gouvernance fragile).

---

### 3.4 Scoping multi‑tenant non uniformisé (élevé)
**Constat**
- Certaines policies vérifient `organization_id`, d’autres non.  
- Des requêtes métiers ne filtrent pas systématiquement par org.  
- RLS dépend de `SetTenantSession`, mais cela n’est pas une garantie si des requêtes sont mal formées ou si des jobs n’injectent pas le contexte.

**Impact**
- Risque d’exposition inter‑organisation si un module omet le scope.  

**Risque**: Élevé.

---

### 3.5 Routes non mappées et stratégie “fail‑open” en dev (moyen)
**Constat**
- `EnterprisePermissionMiddleware` laisse passer les routes non mappées en dev.  
- En prod, un oubli dans le mapping bloque la route.

**Impact**
- Incohérences environment → risque de fail‑open en dev et fail‑closed en prod.  

**Risque**: Moyen.

---

### 3.6 Sécurité des imports/exports (moyen)
**Constat**
- Import CSV (drivers) et exports PDF/Excel semblent exposés à des payloads mal formés.  
- Vérifications d’autorisation parfois dispersées.  

**Impact**
- Injection de données invalides, corruption logique, risques de leakage de données.  

**Risque**: Moyen.

---

### 3.7 Journaux sécurité et audit (moyen)
**Constat**
- Logging centralisé existe, mais absence de normalisation des événements.  
- Pas de modèle “Security Event Taxonomy”.

**Impact**
- Difficile d’auditer à l’échelle entreprise.  

**Risque**: Moyen.

---

### 3.8 Gestion des mots de passe/identités (moyen)
**Constat**
- Passwords générés et affichés en clair (même si temporaire).  
- Pas d’obligation de changement au premier login.  

**Impact**
- Risque opérationnel: partage de secrets non maîtrisé.  

**Risque**: Moyen.

---

### 3.9 UI/UX sécurité (moyen)
**Constat**
- Menus affichant des items non accessibles (masquage partiel).  
- Certaines actions visibles même si interdites.

**Impact**
- Frictions + exposition d’actions non autorisées.  

**Risque**: Moyen.

---

### 3.10 Contrôles API/Livewire (à confirmer)
**Constat**
- Livewire + contrôleurs API ont des autorisations imbriquées.  
- Vérifier que chaque endpoint API respecte l’organisation et la permission.

**Impact**
- Risque d’accès latéral si un endpoint n’applique pas le scope.  

**Risque**: À confirmer.

---

## 4) Recommandations (plan d’amélioration entreprise‑grade)

### Phase 1 – Stabilisation du modèle d’autorisation (Priorité P0)
1) **Standardiser la nomenclature des permissions**  
   - Choisir une seule convention (ex: `resource.action` OU `action resource`).  
   - Migrer toutes les permissions vers la convention choisie.  
2) **Centraliser la validation d’accès**  
   - Définir **un point unique**: policy + gate OU middleware + policy, pas les deux.  
3) **Éliminer les rôles hard‑codés**  
   - Créer un mapping stable (`RoleEnum` ou table `role_aliases`).  
   - Interdire les checks `hasRole('X')` dispersés.  
4) **Scoping d’organisation obligatoire**  
   - Ajouter un scope global par modèle sensible.  
   - Valider `organization_id` dans toutes les policies critiques.

---

### Phase 2 – Renforcement sécurité multi‑tenant (Priorité P1)
1) **RLS PostgreSQL aligné**  
   - Vérifier que tous les modules utilisent les variables `app.current_organization_id`.  
2) **Jobs/Queues**  
   - Injecter explicitement le contexte org dans les jobs (safety).  
3) **Tests de non‑régression multi‑tenant**  
   - Créer des tests “cross‑org data isolation”.

---

### Phase 3 – Sécurisation des flux sensibles (Priorité P1)
1) **Imports CSV**  
   - Validation stricte + file scanning + format normalization.  
2) **Exports**  
   - Filtrage organisationnel strict + audit trail.  
3) **Suppression/Archivage**  
   - Confirmation, audit, signature des actions sensibles.

---

### Phase 4 – Gouvernance & Observabilité (Priorité P2)
1) **Taxonomie d’événements sécurité**  
   - Ex: `AUTHZ_DENIED`, `ROLE_ESCALATION_ATTEMPT`, `CROSS_ORG_READ_ATTEMPT`.  
2) **Dashboards audit**  
   - Vue “Security Health” avec alertes.

---

## 5) Plan de validation (à exécuter)

1) **Inventaire des permissions**  
   - Exporter la liste actuelle → détecter les doublons sémantiques.  
2) **Matrice de cohérence**  
   - Permissions ↔ Routes ↔ Policies ↔ UI.  
3) **Tests multi‑tenant**  
   - Accès cross‑org sur toutes les entités sensibles.  
4) **Tests de régression**  
   - S’assurer que les rôles existants ne sont pas cassés.

---

## 6) Annexes – Indicateurs de maturité

**Maturité actuelle estimée**: 2.5 / 5  
**Objectif entreprise‑grade**: 4.5 / 5  

**Priorités immédiates**:  
- Standardisation permissions & suppression hard‑coding  
- Scoping org + tests isolés  

---

**FIN DU DOCUMENT**  
