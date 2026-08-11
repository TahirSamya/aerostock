# AeroStock

Application de gestion du stock interne de pièces et équipements (matériel informatique, équipement de sécurité, fournitures de bureau, produits d'entretien), réalisée par nous deux, mon frère et moi, dans le cadre de notre projet de fin d'études à l'ONDA.

Auteurs : Walid Tahir et Samya Tahir
Encadrant : Mohamed Amine Elhachimi

Ce projet est un prototype académique inspiré du contexte ONDA, pas un système officiel connecté aux infrastructures réelles.

## Technologies utilisées

Laravel 13, PHP 8.5, Blade, Bootstrap 5, Chart.js, MySQL (via XAMPP)

## Installation

1. Créer un projet Laravel avec composer create-project laravel/laravel aerostock, puis composer require barryvdh/laravel-dompdf
2. Copier les dossiers app, database, resources/views, public/css et le fichier routes/web.php de ce dépôt par-dessus
3. Dans bootstrap/app.php, enregistrer les deux middlewares du projet : admin (EnsureUserIsAdmin) et ShareGlobalStockData
4. Configurer le fichier .env avec les identifiants de la base de données aerostock, et mettre SESSION_DRIVER sur file
5. Créer une base de données vide nommée aerostock dans phpMyAdmin
6. Lancer les commandes php artisan migrate:fresh --seed puis php artisan serve
7. Ouvrir http://127.0.0.1:8000

## Comptes de démonstration

Admin      : admin@onda.ma,         mot de passe: password
Magasinier : s.ouazzani@onda.ma,    mot de passe: password1
Magasinier : k.benaissa@onda.ma,    mot de passe: password2

## Fonctionnalités principales

Articles avec référence générée automatiquement par catégorie, seuil
d'alerte, niveau de criticité et jauge de stock avec capacité maximale.

Mouvements de stock (entrée, sortie, ajustement manuel réservé à l'admin)
et transferts entre emplacements.

Commandes fournisseurs avec mise à jour automatique du stock à la
réception.

Tableau de bord avec graphiques, alertes triées par urgence, statistiques
de consommation, recherche rapide et notifications par email en cas de
rupture d'un article critique.

Export de l'inventaire et des mouvements en CSV et en PDF.

Deux rôles utilisateurs : admin (accès complet) et magasinier (usage quotidien, sans les actions sensibles).
