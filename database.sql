-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 20 juil. 2026 à 21:46
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `aerostock`
--

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `code` varchar(10) DEFAULT NULL COMMENT 'Préfixe utilisé pour générer automatiquement la référence des articles (ex: SEC, INFO)',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `code`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Fournitures de bureau', 'BUR', 'Papeterie et consommables pour les services administratifs', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(2, 'Matériel informatique', 'INFO', 'Ordinateurs, imprimantes et accessoires informatiques', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(3, 'Équipement de sécurité', 'SEC', 'Matériel de protection individuelle et de sécurité incendie', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(4, 'Produits d\'entretien', 'ENT', 'Produits de nettoyage et d\'hygiène pour les locaux', '2026-07-20 17:48:39', '2026-07-20 17:48:39');

-- --------------------------------------------------------

--
-- Structure de la table `commandes_fournisseurs`
--

CREATE TABLE `commandes_fournisseurs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fournisseur_id` bigint(20) UNSIGNED NOT NULL,
  `produit_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `quantite_commandee` int(11) NOT NULL,
  `quantite_recue` int(11) NOT NULL DEFAULT 0,
  `statut` enum('en_attente','partiellement_recue','recue','annulee') NOT NULL DEFAULT 'en_attente',
  `prix_unitaire` decimal(10,2) NOT NULL DEFAULT 0.00,
  `date_commande` date NOT NULL,
  `date_reception` date DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commandes_fournisseurs`
--

INSERT INTO `commandes_fournisseurs` (`id`, `fournisseur_id`, `produit_id`, `user_id`, `quantite_commandee`, `quantite_recue`, `statut`, `prix_unitaire`, `date_commande`, `date_reception`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 1, 20, 0, 'en_attente', 450.00, '2026-07-18', NULL, 'Réapprovisionnement suite à alerte stock bas', '2026-07-20 17:48:39', '2026-07-20 17:48:39');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `fournisseurs`
--

CREATE TABLE `fournisseurs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fournisseurs`
--

INSERT INTO `fournisseurs` (`id`, `nom`, `telephone`, `email`, `adresse`, `created_at`, `updated_at`) VALUES
(1, 'Bureau Plus Maroc', '05 22 24 56 78', 'contact@bureauplus.ma', '12 Rue Ibnou Sina, Casablanca', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(2, 'InfoTech Solutions', '05 22 33 44 55', 'ventes@infotech-solutions.ma', '45 Boulevard Zerktouni, Casablanca', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(3, 'Proteg Sécurité', '05 22 67 89 10', 'commercial@proteg-securite.ma', '8 Rue Al Massira, Casablanca', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(4, 'Clean Services Maroc', '05 22 11 22 33', 'contact@cleanservices.ma', 'Zone Industrielle, Aïn Sebaâ, Casablanca', '2026-07-20 17:48:39', '2026-07-20 17:48:39');

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_01_000001_create_categories_table', 1),
(5, '2026_01_01_000002_create_fournisseurs_table', 1),
(6, '2026_01_01_000003_create_produits_table', 1),
(7, '2026_01_01_000004_create_mouvements_stock_table', 1),
(8, '2026_01_02_000001_add_role_to_users_table', 1),
(9, '2026_01_03_000001_create_transferts_stock_table', 1),
(10, '2026_01_03_000002_add_ajustement_to_mouvements_stock_table', 1),
(11, '2026_01_04_000001_create_commandes_fournisseurs_table', 1),
(12, '2026_01_05_000001_add_code_to_categories_table', 1),
(13, '2026_01_05_000002_add_quantite_max_to_produits_table', 1);

-- --------------------------------------------------------

--
-- Structure de la table `mouvements_stock`
--

CREATE TABLE `mouvements_stock` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `produit_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('entree','sortie','ajustement') NOT NULL,
  `quantite` int(11) NOT NULL,
  `ancienne_quantite` int(11) DEFAULT NULL,
  `nouvelle_quantite` int(11) DEFAULT NULL,
  `motif` varchar(255) DEFAULT NULL,
  `date_mouvement` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `mouvements_stock`
--

INSERT INTO `mouvements_stock` (`id`, `produit_id`, `user_id`, `type`, `quantite`, `ancienne_quantite`, `nouvelle_quantite`, `motif`, `date_mouvement`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'entree', 8, NULL, NULL, 'Stock initial', '2026-07-14', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(2, 2, 1, 'entree', 3, NULL, NULL, 'Stock initial', '2026-07-15', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(3, 3, 1, 'entree', 26, NULL, NULL, 'Stock initial', '2026-07-13', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(4, 4, 1, 'entree', 150, NULL, NULL, 'Stock initial', '2026-07-14', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(5, 5, 1, 'entree', 2, NULL, NULL, 'Stock initial', '2026-07-13', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(6, 6, 1, 'entree', 4, NULL, NULL, 'Stock initial', '2026-07-17', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(7, 7, 1, 'entree', 15, NULL, NULL, 'Stock initial', '2026-07-11', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(8, 8, 1, 'entree', 30, NULL, NULL, 'Stock initial', '2026-07-08', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(9, 9, 1, 'entree', 12, NULL, NULL, 'Stock initial', '2026-07-12', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(10, 10, 1, 'entree', 5, NULL, NULL, 'Stock initial', '2026-07-08', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(11, 3, 1, 'sortie', 3, NULL, NULL, 'Distribution aux services', '2026-07-18', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(12, 1, 1, 'sortie', 1, NULL, NULL, 'Distribution aux services', '2026-07-17', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(13, 5, 1, 'sortie', 3, NULL, NULL, 'Distribution aux services', '2026-07-19', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(14, 2, 1, 'sortie', 2, NULL, NULL, 'Distribution aux services', '2026-07-20', '2026-07-20 17:48:39', '2026-07-20 17:48:39');

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `fournisseur_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantite` int(11) NOT NULL DEFAULT 0,
  `seuil_alerte` int(11) NOT NULL DEFAULT 5,
  `quantite_max` int(11) DEFAULT NULL COMMENT 'Capacité / stock cible souhaité pour cet article — sert de référence à 100% pour la jauge de stock',
  `prix_achat` decimal(10,2) NOT NULL DEFAULT 0.00,
  `prix_vente` decimal(10,2) NOT NULL DEFAULT 0.00,
  `emplacement` varchar(255) DEFAULT NULL COMMENT 'Hangar, zone piste, magasin technique...',
  `criticite` enum('normal','critique') NOT NULL DEFAULT 'normal' COMMENT 'Critique = pièce impactant directement la sécurité/disponibilité opérationnelle',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `reference`, `category_id`, `fournisseur_id`, `quantite`, `seuil_alerte`, `quantite_max`, `prix_achat`, `prix_vente`, `emplacement`, `criticite`, `created_at`, `updated_at`) VALUES
(1, 'Ordinateur de bureau', 'INFO-001', 2, 2, 8, 3, 10, 5200.00, 6200.00, 'Magasin informatique', 'normal', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(2, 'Imprimante laser', 'INFO-002', 2, 2, 3, 2, 6, 1800.00, 2100.00, 'Magasin informatique', 'normal', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(3, 'Cartouche toner', 'INFO-003', 2, 2, 26, 10, 40, 450.00, 520.00, 'Magasin informatique', 'normal', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(4, 'Ramette papier A4', 'BUR-001', 1, 1, 150, 50, 300, 28.00, 35.00, 'Magasin général', 'normal', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(5, 'Chaise de bureau', 'BUR-002', 1, 1, 2, 5, 15, 650.00, 780.00, 'Magasin général', 'normal', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(6, 'Extincteur portable 6kg', 'SEC-001', 3, 3, 4, 10, 25, 320.00, 380.00, 'Local technique', 'critique', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(7, 'Gilet de sécurité haute visibilité', 'SEC-002', 3, 3, 15, 20, 50, 45.00, 60.00, 'Magasin général', 'normal', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(8, 'Gants de protection (paire)', 'SEC-003', 3, 3, 30, 15, 60, 15.00, 22.00, 'Magasin général', 'normal', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(9, 'Produit désinfectant sol 5L', 'ENT-001', 4, 4, 12, 8, 30, 55.00, 70.00, 'Local entretien', 'normal', '2026-07-20 17:48:39', '2026-07-20 17:48:39'),
(10, 'Papier essuie-mains (carton)', 'ENT-002', 4, 4, 5, 10, 25, 90.00, 110.00, 'Local entretien', 'normal', '2026-07-20 17:48:39', '2026-07-20 17:48:39');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0HDIL4E3MkYlnp28TnMKaLkxbsMhD3kjDb9DpGvi', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJSZ3cxZWIzem9mY1JWaHVJdnU1RHhkYk8yWkRwUTNVRktSU2lMNTFYIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=', 1784573802);

-- --------------------------------------------------------

--
-- Structure de la table `transferts_stock`
--

CREATE TABLE `transferts_stock` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `produit_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `emplacement_source` varchar(255) DEFAULT NULL,
  `emplacement_destination` varchar(255) NOT NULL,
  `quantite` int(11) NOT NULL,
  `date_transfert` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','magasinier') NOT NULL DEFAULT 'magasinier',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Tahir', 'admin@aerostock.ma', 'admin', NULL, '$2y$12$rVUkjNVXcFueQhkuVq.1oOMj0Jxwccbpy4yaBX38HONetHVswsyYy', NULL, '2026-07-20 17:48:38', '2026-07-20 17:48:38'),
(2, 'Salma Ouazzani', 's.ouazzani@onda-stock.ma', 'magasinier', NULL, '$2y$12$N6fNsg2ZGHQbM9bpKLGpt.CvdXSuU7QDlkFmNV5Mo6gC7k62lf..y', NULL, '2026-07-20 17:48:38', '2026-07-20 17:48:38'),
(3, 'Karim Benaissa', 'k.benaissa@onda-stock.ma', 'magasinier', NULL, '$2y$12$akTq6sEtSGSTBDjuv7h9KO3q25O/pcE4peG3cg7oTmSA3ZbZzCIwq', NULL, '2026-07-20 17:48:39', '2026-07-20 17:48:39');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commandes_fournisseurs`
--
ALTER TABLE `commandes_fournisseurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `commandes_fournisseurs_fournisseur_id_foreign` (`fournisseur_id`),
  ADD KEY `commandes_fournisseurs_produit_id_foreign` (`produit_id`),
  ADD KEY `commandes_fournisseurs_user_id_foreign` (`user_id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Index pour la table `fournisseurs`
--
ALTER TABLE `fournisseurs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mouvements_stock`
--
ALTER TABLE `mouvements_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mouvements_stock_produit_id_foreign` (`produit_id`),
  ADD KEY `mouvements_stock_user_id_foreign` (`user_id`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `produits_reference_unique` (`reference`),
  ADD KEY `produits_category_id_foreign` (`category_id`),
  ADD KEY `produits_fournisseur_id_foreign` (`fournisseur_id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `transferts_stock`
--
ALTER TABLE `transferts_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transferts_stock_produit_id_foreign` (`produit_id`),
  ADD KEY `transferts_stock_user_id_foreign` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `commandes_fournisseurs`
--
ALTER TABLE `commandes_fournisseurs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `fournisseurs`
--
ALTER TABLE `fournisseurs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `mouvements_stock`
--
ALTER TABLE `mouvements_stock`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `transferts_stock`
--
ALTER TABLE `transferts_stock`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commandes_fournisseurs`
--
ALTER TABLE `commandes_fournisseurs`
  ADD CONSTRAINT `commandes_fournisseurs_fournisseur_id_foreign` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commandes_fournisseurs_produit_id_foreign` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commandes_fournisseurs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `mouvements_stock`
--
ALTER TABLE `mouvements_stock`
  ADD CONSTRAINT `mouvements_stock_produit_id_foreign` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mouvements_stock_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `produits_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `produits_fournisseur_id_foreign` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseurs` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `transferts_stock`
--
ALTER TABLE `transferts_stock`
  ADD CONSTRAINT `transferts_stock_produit_id_foreign` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transferts_stock_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
