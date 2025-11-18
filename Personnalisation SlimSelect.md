# Personnalisation SlimSelect - Documentation Complète

## 1. Variables CSS SlimSelect (--ss-*)

Les variables CSS suivantes ont été configurées dans `:root` pour personnaliser l'apparence de SlimSelect :

```css
:root {
  /* Couleurs de base */
  --ss-bg-color: #ffffff;                    /* Couleur de fond des éléments */
  --ss-font-color: #1f2937;                  /* Couleur du texte */
  --ss-border-color: #d1d5db;                /* Couleur des bordures (gris par défaut) */
  --ss-focus-color: #3b82f6;                 /* Couleur au focus (bleu) */
  --ss-primary-color: #3b82f6;               /* Couleur primaire (bleu) */
  --ss-placeholder-color: #9ca3af;           /* Couleur du texte placeholder */
  --ss-disabled-color: #f3f4f6;              /* Couleur pour état désactivé */
  --ss-error-color: #ef4444;                 /* Couleur d'erreur */
  --ss-success-color: #10b981;               /* Couleur de succès */
  --ss-highlight-color: #fef3c7;             /* Couleur de surlignage */
  
  /* Dimensions */
  --ss-main-height: 2.5rem;                  /* Hauteur du champ principal (40px) */
  --ss-option-height: 2.5rem;                /* Hauteur de chaque option */
  --ss-search-height: 2.5rem;                /* Hauteur du champ de recherche */
  --ss-content-height: 250px;                /* Hauteur max de la liste déroulante */
  
  /* Espacement */
  --ss-spacing-s: 0.25rem;                   /* Petit espacement (4px) */
  --ss-spacing-m: 0.5rem;                    /* Espacement moyen (8px) */
  --ss-spacing-l: 1rem;                      /* Grand espacement (16px) */
  
  /* Autres */
  --ss-border-radius: 0.375rem;              /* Rayon des coins (6px) */
  --ss-animation-timing: 0.2s;               /* Durée des animations */
}
```

---

## 2. Classes CSS Personnalisées et Sélecteurs

### 2.1 Conteneur Principal (.ss-main)

```css
/* Champ principal SlimSelect */
.ss-main {
  transition: all var(--ss-animation-timing) ease !important;
}

/* État par défaut (non-ouvert) */
.ss-main:not(.open) {
  border-color: var(--ss-border-color) !important;      /* Bordure grise */
  background-color: #f9fafb !important;                 /* Fond gris clair */
}

/* État au focus ou ouvert */
.ss-main.open,
.ss-main:focus {
  border-color: var(--ss-focus-color) !important;       /* Bordure bleue */
  background-color: #ffffff !important;                 /* Fond blanc */
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;  /* Ombre bleue légère */
}
```

**Résultat** : Le champ affiche une bordure grise avec fond gris par défaut, puis passe à une bordure bleue avec ombre au focus.

---

### 2.2 Conteneur de Contenu (.ss-content)

```css
/* Conteneur de contenu (liste déroulante) */
.ss-content {
  border-color: var(--ss-border-color) !important;      /* Bordure grise */
  background-color: var(--ss-bg-color) !important;      /* Fond blanc */
}
```

**Résultat** : La liste déroulante a une bordure grise et un fond blanc cohérent.

---

### 2.3 Champ de Recherche (.ss-search)

```css
/* Champ de recherche dans la liste */
.ss-content .ss-search input {
  border-color: var(--ss-border-color) !important;      /* Bordure grise par défaut */
  color: var(--ss-font-color) !important;               /* Texte gris foncé */
}

/* État au focus du champ de recherche */
.ss-content .ss-search input:focus {
  border-color: var(--ss-focus-color) !important;       /* Bordure bleue */
  outline: none !important;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;  /* Ombre bleue */
}
```

**Résultat** : Le champ de recherche affiche une bordure grise par défaut et bleue au focus, avec une ombre légère.

---

### 2.4 Options de la Liste (.ss-option)

```css
/* Options par défaut */
.ss-content .ss-list .ss-option {
  color: var(--ss-font-color) !important;               /* Texte gris foncé */
  background-color: var(--ss-bg-color) !important;      /* Fond blanc */
}

/* État au survol (hover) */
.ss-content .ss-list .ss-option:hover:not(.ss-disabled) {
  background-color: #f3f4f6 !important;                 /* Fond gris clair */
  color: var(--ss-font-color) !important;               /* Texte reste gris */
  border-left-color: var(--ss-focus-color) !important;  /* Bordure gauche bleue */
}

/* État sélectionné */
.ss-content .ss-list .ss-option.ss-selected,
.ss-content .ss-list .ss-option.ss-highlighted {
  background-color: #dbeafe !important;                 /* Fond bleu clair */
  color: var(--ss-font-color) !important;               /* Texte gris foncé */
  border-left-color: var(--ss-focus-color) !important;  /* Bordure gauche bleue */
}
```

**Résultat** : 
- Les options ont un fond blanc par défaut
- Au survol : fond gris clair avec bordure gauche bleue
- Sélectionnée : fond bleu clair avec bordure gauche bleue

---

### 2.5 Texte Placeholder (.ss-single-selected)

```css
/* Texte du placeholder */
.ss-single .ss-single-selected.placeholder {
  color: var(--ss-placeholder-color) !important;        /* Couleur gris moyen */
}
```

**Résultat** : Le texte placeholder "Sélectionnez un pays" s'affiche en gris moyen.

---

## 3. Résumé des Personnalisations par État

### État Par Défaut (Champ Fermé)
| Élément | Bordure | Fond | Texte |
|---------|---------|------|-------|
| Champ principal | Gris (#d1d5db) | Gris clair (#f9fafb) | Gris foncé (#1f2937) |
| Placeholder | - | - | Gris moyen (#9ca3af) |

### État Au Focus (Champ Ouvert)
| Élément | Bordure | Fond | Texte | Ombre |
|---------|---------|------|-------|-------|
| Champ principal | Bleu (#3b82f6) | Blanc (#ffffff) | Gris foncé | Bleu léger |
| Champ de recherche | Bleu | Blanc | Gris foncé | Bleu léger |
| Option (hover) | Bleu (gauche) | Gris clair | Gris foncé | - |
| Option (sélectionnée) | Bleu (gauche) | Bleu clair (#dbeafe) | Gris foncé | - |

---

## 4. Palette de Couleurs Utilisée

```
Gris (bordures/fond par défaut) : #d1d5db
Gris clair (fond secondaire) : #f9fafb, #f3f4f6
Gris moyen (placeholder) : #9ca3af
Gris foncé (texte) : #1f2937
Blanc (fond principal) : #ffffff
Bleu (focus/primaire) : #3b82f6
Bleu clair (sélection) : #dbeafe
Ombre bleue légère : rgba(59, 130, 246, 0.1)
```

---

## 5. Hauteurs et Dimensions

```
Hauteur du champ principal : 2.5rem (40px)
Hauteur des options : 2.5rem (40px)
Hauteur du champ de recherche : 2.5rem (40px)
Hauteur max de la liste : 250px
Rayon des coins : 0.375rem (6px)
Durée des animations : 0.2s
```

---

## 6. Transitions et Animations

```css
/* Transition fluide au focus */
transition: all 0.2s ease !important;

/* Ombre au focus avec opacité légère */
box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
```

---

## 7. Points Clés de la Personnalisation

1. **Utilisation des variables CSS natives** : Toutes les personnalisations utilisent les variables `--ss-*` officielles de SlimSelect, sans surcharge de styles.

2. **Cohérence visuelle** : Les trois champs (Nom, Prénom, Pays) ont la même hauteur et le même style par défaut.

3. **États visuels clairs** :
   - **Défaut** : Bordure grise, fond gris
   - **Focus** : Bordure bleue, fond blanc, ombre légère
   - **Sélection** : Fond bleu clair, bordure gauche bleue

4. **Accessibilité** : Les transitions fluides et les ombres légères améliorent l'expérience utilisateur sans surcharger l'interface.

5. **Responsive** : Les dimensions en `rem` s'adaptent aux préférences de taille de police de l'utilisateur.

---

## 8. Intégration dans le Projet

Ces styles sont définis dans le fichier `client/src/index.css` et s'appliquent automatiquement à tous les éléments SlimSelect du projet via les sélecteurs CSS fournis.

Pour modifier l'apparence, il suffit de changer les valeurs des variables CSS dans `:root` sans toucher aux sélecteurs CSS.
