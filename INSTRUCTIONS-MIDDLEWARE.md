# Enregistrer le middleware "admin" (obligatoire)

Ouvre le fichier `bootstrap/app.php` à la racine de ton projet Laravel.
Tu dois voir quelque chose comme ceci :

```php
->withMiddleware(function (Middleware $middleware) {
    //
})
```

Remplace-le par (ajoute la ligne à l'intérieur) :

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    ]);
})
```

Sans cette étape, Laravel ne saura pas ce que signifie `->middleware('admin')`
dans les routes, et affichera une erreur "Target class [admin] does not exist."
