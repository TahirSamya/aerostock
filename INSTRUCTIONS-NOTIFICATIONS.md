# Notifications — cloche d'alerte globale

Pour afficher le nombre de produits en alerte sur TOUTES les pages (pas juste le
dashboard), on utilise un "View Composer" : une fonction qui calcule une donnée
et la rend disponible dans le layout, peu importe la page affichée.

## Fichier à modifier : app/Providers/AppServiceProvider.php

Ouvre ce fichier. Tu dois voir quelque chose comme :

```php
public function boot(): void
{
    //
}
```

Remplace le contenu de `boot()` par :

```php
public function boot(): void
{
    View::composer('layouts.app', function ($view) {
        if (auth()->check()) {
            $count = \App\Models\Produit::whereColumn('quantite', '<=', 'seuil_alerte')->count();
            $view->with('alertesCount', $count);
        } else {
            $view->with('alertesCount', 0);
        }
    });
}
```

Et ajoute en haut du fichier, avec les autres `use` :

```php
use Illuminate\Support\Facades\View;
```
