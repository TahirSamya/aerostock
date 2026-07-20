# AeroStock

Application de gestion de stock développée dans le cadre de notre PFE. Le thème choisi est un contexte aéroportuaire, mais il s'agit d'un prototype académique, pas d'un système connecté à l'ONDA ou à des infrastructures réelles.

## Stack

Laravel, Blade, Bootstrap 5, Chart.js, MySQL

## Installation

1. Créer le projet Laravel
```bash
composer create-project laravel/laravel aerostock
cd aerostock
```

2. Copier les fichiers de ce dépôt dans le projet :
- `database/migrations/*`
- `database/seeders/DatabaseSeeder.php`
- `app/Models/*`
- `app/Http/Controllers/*`
- `routes/web.php` 
- `resources/views/*`
- `public/css/custom.css`

3. Configurer la base de données dans `.env`
```
DB_CONNECTION=mysql
DB_DATABASE=aerostock
DB_USERNAME=root
DB_PASSWORD=
```

4. Migrer et peupler la base
```bash
php artisan migrate:fresh --seed
```

5. Lancer le serveur
```bash
php artisan serve
```
Accès sur `http://127.0.0.1:8000`, compte : admin@aerostock.ma / password

## Fonctionnalités

- Authentification avec deux rôles : administrateur et magasinier
- Dashboard avec graphiques (mouvements des 7 derniers jours, répartition par catégorie) et notifications d'alerte stock
- Gestion des pièces avec emplacement, niveau de criticité et quantité max
- Mouvements de stock (entrée/sortie) avec vérification du stock disponible et annulation possible
- Ajustements manuels de stock (admin)
- Transferts de stock entre emplacements
- Commandes fournisseurs avec suivi de statut et réception partielle ou totale
- Export CSV et PDF
- Gestion des utilisateurs, catégories et fournisseurs


