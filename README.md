# AeroStock

Application web de gestion de stock interne, développée dans le cadre d'un Projet de Fin d'Études au sein de l'Office National Des Aéroports (ONDA).

# Contexte

Le suivi du stock de pièces et de matériel se faisait jusqu'ici de façon manuelle. AeroStock remplace ce suivi par une application centralisée qui permet de connaître à tout moment l'état du stock, d'enregistrer chaque mouvement (entrée, sortie, ajustement) et de garder une trace de qui a fait quoi.

Quatre familles d'articles sont concernées :

- Pièces informatiques
- Équipements de sécurité
- Fournitures de bureau
- Produits d'entretien

# Stack technique

- *Backend* : Laravel (PHP 8)
- *Base de données* : MySQL 
- *Frontend* : Blade, Bootstrap 5
- *Environnement de développement* : XAMPP
- *Versionnage* : Git / GitHub

# Prérequis techniques

- PHP 8.1 ou supérieur
- Extensions PHP : OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath (incluses par défaut dans XAMPP)
- Composer
- MySQL / MariaDB (fourni par XAMPP)
- Node.js et npm, uniquement si la compilation des styles via Vite/Tailwind est utilisée — l'interface repose principalement sur `public/css/custom.css`, chargé directement sans étape de build

# Rôles

L'application distingue deux profils : *Administrateur* et *Magasinier*. Le contrôle des droits n'est pas seulement fait au niveau de l'affichage (masquer un bouton) mais aussi côté serveur, via un middleware dédié, pour empêcher qu'une action réservée à l'admin soit exécutée en accédant directement à une URL.

| Action                                                        | Admin | Magasinier |
|---------------------------------------------------------------|-------|------------|
| Consulter produits, mouvements, transferts, commandes         | oui   | oui        |
| Créer une entrée / sortie de stock                            | oui   | oui        |
| Créer / réceptionner une commande fournisseur                 | oui   | oui        |
| Ajouter ou modifier un produit, un fournisseur, une catégorie | oui   | oui        |
| Supprimer un produit, un mouvement, une commande              | oui   | non        |
| Ajustement manuel de stock                                    | oui   | non        |
| Gestion des comptes utilisateurs                              | oui   | non        |

# Fonctionnalités

*Authentification*
Connexion, déconnexion, gestion des comptes (réservée à l'admin : création, modification, réinitialisation de mot de passe, suppression).

*Tableau de bord*
Indicateurs clés (valeur totale du stock, nombre d'articles référencés, articles en alerte), liste des produits sous leur seuil d'alerte, produits marqués « critique », derniers mouvements enregistrés, graphique de l'évolution des entrées/sorties.

*Produits*
Ajout, modification, suppression d'un article. La référence est générée automatiquement en fonction de la catégorie (par exemple `SEC-001` pour un équipement de sécurité). Chaque produit a une quantité, un seuil d'alerte, un stock maximal, un niveau de criticité (normal / critique) et un historique de ses prix d'achat.

*Mouvements de stock*
Entrées et sorties, avec blocage si la quantité demandée en sortie dépasse le stock disponible. Chaque mouvement conserve l'utilisateur, la date, le type et, pour les sorties et ajustements, un motif obligatoire.

*Transferts*
Déplacement de stock d'un emplacement à un autre, avec historique des transferts effectués.

*Commandes fournisseurs*
Création d'une commande, réception totale ou partielle. Chaque réception met à jour le stock automatiquement et génère un mouvement d'entrée correspondant, sans double saisie.

*Fournisseurs et catégories*
Gestion classique (ajout, modification, suppression), avec quelques garde-fous : impossible de créer deux fournisseurs avec le même nom, impossible de supprimer une catégorie encore utilisée par des produits.

*Statistiques*
Valeur totale du stock, nombre d'articles en alerte ou en rupture, top des articles les plus consommés sur une période choisie.

*Alertes*
Une pastille dans la barre supérieure signale en temps réel tout produit passé sous son seuil. Un email est envoyé automatiquement aux comptes admin lorsqu'un produit marqué « critique » atteint un stock nul (fonctionnement testable via le mode `log` de Laravel en environnement local, le contenu de l'email étant alors visible dans `storage/logs/laravel.log`).

*Exports*
Export CSV et Excel selon les modules (produits, mouvements).

*Interface*
Sidebar de navigation organisée par sections (Pilotage, Stock, Achats, Administration), interface adaptée aux écrans mobiles et tablettes avec menu repliable.

# Structure du projet

```
app/
  Http/
    Controllers/     logique métier de chaque module
    Middleware/       contrôle d'accès admin, calcul des alertes globales
  Models/             entités : Produit, MouvementStock, Fournisseur, CommandeFournisseur...
  Mail/               notification email de rupture de stock critique
resources/
  views/               une vue par module (produits, mouvements, transferts, commandes...)
routes/
  web.php              déclaration de toutes les routes
database/
  migrations/          structure des tables
  seeders/             comptes de démonstration et données de test
```

# État d'avancement

L'ajustement manuel de stock (correction d'un écart constaté lors d'un inventaire physique, réservé à l'admin) est en cours de réintégration suite à une réorganisation du module Mouvements.

# Pistes d'amélioration et limites connues

- Le circuit de commande fournisseur reste simple : n'importe quel utilisateur peut créer et réceptionner une commande, sans étape de validation préalable par un responsable des achats.
- Deux logiques d'emplacement coexistent sans être totalement unifiées : un champ « emplacement » simple utilisé par le module Transferts, et une répartition plus détaillée du stock par emplacement sur la fiche produit.
- Aucune gestion des unités de mesure (pièce, boîte, litre...) : tous les articles sont comptés de la même façon.
- Pas de traçabilité par lot ou par date de péremption, pourtant pertinente pour certaines pièces à durée de vie limitée.

# Installation

```bash
git clone https://github.com/TahirSamya/aerostock.git
cd aerostock
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

L'application est alors accessible sur `http://127.0.0.1:8000`.

# Comptes de démonstration

| Rôle       | Email              |
| ---------- | ------------------ |
| Admin      | admin@onda.ma      |
| Magasinier | s.ouazzani@onda.ma |
| Magasinier | k.benaissa@onda.ma |

# Réalisation et encadrement

Projet réalisé dans le cadre d'un Projet de Fin d'Études, en partenariat avec l'ONDA.

- **Réalisé par** : Tahir Walid, Tahir Samya
- **Encadrant** : Mohamed Amine Elhachimi
