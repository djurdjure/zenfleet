# 📖 GUIDE UTILISATEUR - VALIDATION KILOMÉTRAGE V2.1

## 🎯 RÈGLES À CONNAÎTRE

### ✅ RELEVÉS ACCEPTÉS
1. **Kilométrage égal ou supérieur** au kilométrage actuel
   - Exemple: Véhicule à 100 000 km → Saisir 100 000 km ou plus ✅

2. **Date/heure STRICTEMENT APRÈS le relevé le plus récent** ⭐ NOUVEAU
   - Exemple: Dernier relevé 22/11 à 14:30 → Saisir 22/11 à 14:31 ou plus tard ✅
   - ⚠️ IMPORTANT: La même date/heure est REFUSÉE

3. **Premier relevé du véhicule**
   - Exemple: Véhicule neuf sans historique → N'importe quel kilométrage ✅

4. **Relevé rétroactif cohérent**
   - Exemple: Relevés 20/11: 100k, 22/11: 110k → Insérer 21/11: 105k ✅

### ❌ RELEVÉS REJETÉS
1. **Kilométrage inférieur** au kilométrage actuel
   - Exemple: Véhicule à 100 000 km → Saisir 95 000 km ❌
   - **Message**: "Le kilométrage saisi (95 000 km) est inférieur au kilométrage actuel du véhicule..."

2. **Date/heure égale ou antérieure** ⭐ NOUVEAU
   - Exemple: Dernier relevé 22/11 à 14:30 → Saisir 22/11 à 14:30 (même heure) ❌
   - Exemple: Dernier relevé 22/11 à 14:30 → Saisir 22/11 à 14:00 (heure antérieure) ❌
   - **Message**: "La date et l'heure du relevé (22/11/2025 à 14:30) doivent être strictement postérieures au relevé le plus récent..."

3. **Relevé rétroactif incohérent**
   - Exemple: Relevés 20/11: 100k, 22/11: 110k → Insérer 21/11: 115k ❌
   - **Message**: "Un relevé kilométrique ultérieur existe déjà avec 110 000 km..."

---

## 💡 CAS D'USAGE COURANTS

### Cas #1: Relevé quotidien normal
```
Situation:
- Véhicule 284139-16 à 100 000 km
- Fin de journée: compteur affiche 100 450 km

Action:
1. Sélectionner le véhicule
2. Saisir: 100 450 km
3. Sélectionner date/heure actuelles
4. Cliquer "Enregistrer"

Résultat: ✅ SUCCÈS
```

### Cas #2: Erreur de saisie détectée
```
Situation:
- Véhicule 284139-16 à 100 000 km
- Tentative de saisir: 95 000 km (erreur de frappe)

Action:
1. Saisir: 95 000 km
2. Cliquer "Enregistrer"

Résultat: ❌ REJETÉ
Message: "Le kilométrage saisi (95 000 km) est inférieur au kilométrage actuel du véhicule 284139-16 (100 000 km). Veuillez saisir un kilométrage égal ou supérieur."

Correction:
1. Corriger: 100 500 km
2. Cliquer "Enregistrer"
Résultat: ✅ SUCCÈS
```

### Cas #3: Relevé manquant (rétroactif)
```
Situation:
- Véhicule 284139-16
- 20/11: 100 000 km
- 22/11: 110 000 km
- Oublié de saisir le 21/11 (105 000 km)

Action:
1. Sélectionner le véhicule
2. Saisir: 105 000 km
3. Sélectionner date: 21/11/2025
4. Sélectionner heure: 18:00
5. Cliquer "Enregistrer"

Résultat: ✅ SUCCÈS (105k entre 100k et 110k)
```

### Cas #4: Deux utilisateurs en même temps
```
Situation:
- Véhicule 284139-16 à 100 000 km
- Utilisateur A et B saisissent simultanément

Action:
- Utilisateur A: Saisir 102 000 km (14:00:00)
- Utilisateur B: Saisir 101 000 km (14:00:01)

Résultat:
- Utilisateur A: ✅ SUCCÈS (102k enregistré)
- Utilisateur B: ❌ REJETÉ
  Message: "Le kilométrage saisi (101 000 km) est inférieur au kilométrage actuel du véhicule 284139-16 (102 000 km)..."

Explication:
Le système protège automatiquement contre les doublons grâce au verrouillage de transaction.
```

### Cas #5: Date/heure identique (rejet temporel) ⭐ NOUVEAU
```
Situation:
- Véhicule 284139-16
- Dernier relevé: 22/11/2025 à 14:30 → 100 000 km
- Tentative de saisir un nouveau relevé avec la MÊME heure

Action:
1. Sélectionner le véhicule
2. Saisir: 105 000 km
3. Sélectionner date/heure: 22/11/2025 14:30 (identique au dernier relevé)
4. Cliquer "Enregistrer"

Résultat: ❌ REJETÉ
Message: "La date et l'heure du relevé (22/11/2025 à 14:30) doivent être strictement postérieures au relevé le plus récent du véhicule 284139-16 (22/11/2025 à 14:30). Veuillez saisir une date et heure plus récentes."

Correction:
1. Modifier l'heure: 22/11/2025 14:31 (ou plus tard)
2. Cliquer "Enregistrer"
Résultat: ✅ SUCCÈS

Explication:
Chaque relevé doit avoir une date/heure UNIQUE et STRICTEMENT POSTÉRIEURE
au relevé précédent pour garantir l'ordre chronologique.
```

---

## ⚠️ MESSAGES D'ERREUR ET SOLUTIONS

### Erreur: "Kilométrage inférieur"
**Message complet:**
> Le kilométrage saisi (95 000 km) est inférieur au kilométrage actuel du véhicule 284139-16 (100 000 km). Un relevé kilométrique doit toujours être égal ou supérieur au kilométrage précédent.

**Causes possibles:**
1. Erreur de saisie (faute de frappe)
2. Lecture incorrecte du compteur
3. Tentative de correction d'un ancien relevé

**Solutions:**
1. ✅ Vérifier le compteur du véhicule
2. ✅ Corriger la saisie avec le bon kilométrage
3. ✅ Si le compteur a réellement diminué (compteur remis à zéro, remplacement compteur):
   - Contacter votre administrateur système
   - Ne PAS forcer la saisie

### Erreur: "Date et heure non postérieures" ⭐ NOUVEAU
**Message complet:**
> La date et l'heure du relevé (22/11/2025 à 14:30) doivent être strictement postérieures au relevé le plus récent du véhicule 284139-16 (22/11/2025 à 14:30). Veuillez saisir une date et heure plus récentes.

**Causes possibles:**
1. Date/heure identique au dernier relevé (doublon temporel)
2. Date/heure antérieure au dernier relevé (erreur de saisie)
3. L'horloge système est mal configurée
4. Tentative de saisir deux relevés simultanés

**Solutions:**
1. ✅ Vérifier la date et l'heure du dernier relevé enregistré
2. ✅ Modifier la date/heure pour qu'elle soit APRÈS le dernier relevé
3. ✅ Attendre quelques secondes avant de saisir si vous venez d'enregistrer un relevé
4. ✅ Si vous devez corriger un ancien relevé:
   - Contacter votre superviseur
   - Expliquer la situation
   - Ne PAS forcer la même date/heure

**Important:**
- Même si vous saisissez le bon kilométrage, le système REFUSE si la date/heure n'est pas strictement après le relevé précédent
- Cela garantit que l'historique des relevés soit toujours dans l'ordre chronologique
- Il est IMPOSSIBLE d'avoir deux relevés avec exactement la même date/heure

### Erreur: "Relevé ultérieur existe déjà"
**Message complet:**
> Un relevé kilométrique ultérieur existe déjà avec 110 000 km le 22/11/2025 à 16:00. Le kilométrage saisi (115 000 km) est incohérent.

**Causes possibles:**
1. Tentative d'insérer un relevé rétroactif avec kilométrage trop élevé
2. Erreur de date
3. Erreur de kilométrage

**Solutions:**
1. ✅ Vérifier que le kilométrage est cohérent avec l'historique
2. ✅ Consulter l'historique des relevés du véhicule
3. ✅ Corriger soit la date, soit le kilométrage

---

## 🔍 VÉRIFIER L'HISTORIQUE

### Avant de saisir un relevé
1. Sélectionner le véhicule
2. Regarder le kilométrage actuel affiché
3. S'assurer que le nouveau kilométrage est >= au kilométrage actuel
4. Saisir et enregistrer

### Consulter l'historique complet
1. Aller dans "Relevés Kilométriques"
2. Filtrer par véhicule
3. Voir tous les relevés passés
4. Vérifier la cohérence chronologique

---

## ✅ BONNES PRATIQUES

### Recommandations
1. ✅ **Saisir quotidiennement** les relevés
2. ✅ **Vérifier le compteur** avant de saisir
3. ✅ **Double-vérifier** le kilométrage saisi
4. ✅ **Noter** les relevés anormaux (grand écart)
5. ✅ **Prendre une photo** du compteur si nécessaire

### À éviter
1. ❌ Saisir un kilométrage au hasard
2. ❌ Forcer une valeur sans vérifier
3. ❌ Ignorer les messages d'erreur
4. ❌ Saisir avec plusieurs jours de retard
5. ❌ Corriger manuellement les anciens relevés

---

## 🆘 BESOIN D'AIDE ?

### En cas de problème
1. **Vérifier d'abord** le kilométrage réel du véhicule
2. **Consulter l'historique** des relevés
3. **Contacter** votre superviseur si:
   - Le compteur a été remis à zéro
   - Le compteur a été remplacé
   - Vous constatez une anomalie

### Contact Support
- Email: support@zenfleet.com
- Tel: +XXX XXX XXX XXX
- Dans l'application: Menu → Aide

---

## 📊 COMPRENDRE LES MESSAGES DE SUCCÈS

### Message type
> Kilométrage enregistré avec succès pour 284139-16 : 100 000 km → 105 000 km (+5 000 km)

**Lecture:**
- `284139-16`: Plaque du véhicule
- `100 000 km`: Ancien kilométrage
- `105 000 km`: Nouveau kilométrage
- `+5 000 km`: Distance parcourue

---

**Version**: V2.1
**Date**: 22/11/2025
**Statut**: Production

**Nouveautés V2.1**:
- ⭐ Validation temporelle stricte: Date/heure doit être APRÈS le relevé le plus récent
- ⭐ Protection contre les doublons temporels
- ⭐ Ordre chronologique garanti à 100%
