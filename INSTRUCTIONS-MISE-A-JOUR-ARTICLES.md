# Mise à jour "Pièces & équipements" — à appliquer

## Étapes

```bash
php artisan migrate
```

Cela ajoute :
- `categories.code` : préfixe utilisé pour générer les références (ex: SEC, INFO). Pour les catégories déjà en base, le code est déduit automatiquement des références déjà utilisées par leurs articles (ex: si un article a la référence `SEC-002`, la catégorie récupère le code `SEC`).
- `produits.quantite_max` : capacité cible de l'article, utilisée comme référence "100%" pour la jauge de stock. Pour les articles déjà en base, une valeur de départ est calculée (4x le seuil d'alerte, ou la quantité actuelle si elle est plus grande) — à ajuster ensuite au cas par cas depuis le formulaire d'édition.

Si vous repartez d'une base fraîche :

```bash
php artisan migrate:fresh --seed
```

## Ce qui a changé

1. **Ajout d'article** : le champ Référence n'est plus saisi à la main. Choisir une catégorie affiche automatiquement la prochaine référence disponible (dernier numéro de la catégorie + 1). La génération est aussi recalculée côté serveur pour éviter tout contournement.
2. **Tri** : la liste des articles est désormais triée par catégorie puis par référence, avec un en-tête de groupe par catégorie.
3. **Stock max** : nouveau champ par article (capacité cible). La jauge de stock affiche désormais un vrai pourcentage (`quantité / stock max`) au lieu de saturer à 100% dès que la quantité dépasse 2x le seuil d'alerte.
4. **Alertes (tableau de bord)** : refonte de la liste "Articles en alerte" avec 3 niveaux d'urgence (rupture / critique / bas), triés du plus urgent au moins urgent, avec fournisseur et lien direct vers "Commander".
5. **Catégories** : nouveau champ obligatoire "Code" (préfixe, lettres uniquement) à la création d'une catégorie.
