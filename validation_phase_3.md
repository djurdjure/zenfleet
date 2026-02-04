## ✅ Clarification de test
- Un **404** sur une route inexistante est **normal**.  
- Le **fail‑closed** se valide uniquement sur **routes existantes** et **non mappées** dans le middleware.

### ✅ Rappel pour logs
Consulter les logs dans un éditeur (Bloc‑note, VSCode, etc.) est **parfaitement acceptable**.  
Il n’est pas obligatoire de passer par une interface dédiée.

---

## ✅ Correctif sécurité ajouté (dépôts / permissions)
**Origine réelle trouvée :** l’utilisateur Admin avait des **permissions directes** (assignées dans le passé), ce qui lui donnait accès aux dépôts même après suppression des permissions du rôle.  

**Mesure appliquée :**\n
- Ajout d’un indicateur `use_custom_permissions` (par défaut **false**).\n
- Tant que l’utilisateur **n’active pas explicitement les permissions personnalisées**, seules les permissions **du rôle** sont utilisées.\n
- Les utilisateurs sans rôle mais avec permissions directes conservent ces permissions (pour éviter un blocage total).\n

**À re‑valider :**\n
1. Retirer toutes les permissions « dépôts » au rôle Admin.\n
2. Se reconnecter en Admin.\n
3. Résultat attendu : accès dépôt **refusé** (403) ou actions désactivées.\n
