-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 06 oct. 2025 à 18:11
-- Version du serveur :  10.4.16-MariaDB
-- Version de PHP : 7.3.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gesvehicule`
--

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `fuel_entries`
--

CREATE TABLE `fuel_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `date_remplissage` date NOT NULL,
  `prix_litre` decimal(8,3) NOT NULL,
  `litres` decimal(8,2) NOT NULL,
  `cout_total` decimal(10,2) NOT NULL,
  `kilometrage` int(11) NOT NULL,
  `station` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_carburant` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diesel',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preuve` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fuel_entries`
--

INSERT INTO `fuel_entries` (`id`, `vehicle_id`, `date_remplissage`, `prix_litre`, `litres`, `cout_total`, `kilometrage`, `station`, `type_carburant`, `notes`, `preuve`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-09-27', '550.000', '10.00', '5500.00', 100, 'Total', 'diesel', NULL, NULL, '2025-09-27 13:44:06', '2025-09-27 15:49:52'),
(2, 1, '2025-09-27', '300.000', '20.00', '6000.00', 300, NULL, 'diesel', NULL, NULL, '2025-09-27 16:29:41', '2025-09-27 16:29:41'),
(3, 1, '2025-10-01', '350.000', '30.00', '10500.00', 700, NULL, 'diesel', NULL, NULL, '2025-10-01 10:53:04', '2025-10-01 10:53:04'),
(4, 2, '2025-10-01', '990.000', '30.00', '29700.00', 980000, NULL, 'diesel', NULL, NULL, '2025-10-01 11:49:27', '2025-10-01 11:49:27'),
(5, 2, '2025-10-04', '880.000', '30.00', '26400.00', 980100, NULL, 'diesel', NULL, NULL, '2025-10-04 18:53:27', '2025-10-04 18:53:27'),
(6, 2, '2025-10-06', '755.000', '20.00', '15100.00', 980100, NULL, 'diesel', NULL, NULL, '2025-10-06 15:51:44', '2025-10-06 15:51:44');

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2025_09_26_233232_create_vehicles_table', 2),
(5, '2025_09_27_130930_create_fuel_entries_table', 3),
(6, '2025_09_27_164203_create_repair_logs_table', 4);

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `repair_logs`
--

CREATE TABLE `repair_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `date_intervention` date NOT NULL,
  `type_intervention` enum('entretien_routine','reparation','vidange','freinage','pneumatique','electrique','mecanique','carrosserie','autre') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'entretien_routine',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details_travaux` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cout_main_oeuvre` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cout_pieces` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cout_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `kilometrage_vehicule` int(11) NOT NULL,
  `garage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `technicien` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('planifie','en_cours','termine','annule') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planifie',
  `date_prochaine_revision` date DEFAULT NULL,
  `prochain_kilometrage` int(11) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `repair_logs`
--

INSERT INTO `repair_logs` (`id`, `vehicle_id`, `date_intervention`, `type_intervention`, `description`, `details_travaux`, `cout_main_oeuvre`, `cout_pieces`, `cout_total`, `kilometrage_vehicule`, `garage`, `technicien`, `statut`, `date_prochaine_revision`, `prochain_kilometrage`, `notes`, `facture`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-09-27', 'vidange', 'Vidange', 'neant', '3000.00', '0.00', '3000.00', 300, 'qddqd', 'qsdqds', 'termine', '2025-09-28', 3000, NULL, NULL, '2025-09-27 17:19:12', '2025-09-27 17:19:12'),
(2, 2, '2025-10-06', 'reparation', 'Defaut freinage', NULL, '2000.00', '0.00', '2000.00', 980100, NULL, NULL, 'termine', NULL, NULL, NULL, NULL, '2025-10-06 15:55:08', '2025-10-06 15:55:08');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Ibra Ndiaye', 'ibra789ndiaye@gmail.com', NULL, '$2y$10$77RyjqoPu4hp7NGajd24K.NWBe76tbGb6MXN/L6GnFuGjMlVEcBC.', NULL, '2025-09-27 12:37:08', '2025-09-27 12:37:08');

-- --------------------------------------------------------

--
-- Structure de la table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `immatriculation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marque` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modele` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_vehicule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kilometrage_actuel` int(11) NOT NULL DEFAULT 0,
  `etat` enum('disponible','en_entretien','hors_service') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vehicles`
--

INSERT INTO `vehicles` (`id`, `immatriculation`, `marque`, `modele`, `type_vehicule`, `kilometrage_actuel`, `etat`, `created_at`, `updated_at`) VALUES
(1, 'DK-004-AA', 'BMW', 'S', 'voiture', 700, 'disponible', '2025-09-27 12:55:51', '2025-10-01 10:53:04'),
(2, 'DK-01414', 'BMW', 'S14', 'camion', 980100, 'disponible', '2025-10-01 11:48:34', '2025-10-04 18:53:27');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `fuel_entries`
--
ALTER TABLE `fuel_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fuel_entries_vehicle_id_date_remplissage_index` (`vehicle_id`,`date_remplissage`),
  ADD KEY `fuel_entries_type_carburant_index` (`type_carburant`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Index pour la table `repair_logs`
--
ALTER TABLE `repair_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repair_logs_vehicle_id_date_intervention_index` (`vehicle_id`,`date_intervention`),
  ADD KEY `repair_logs_type_intervention_index` (`type_intervention`),
  ADD KEY `repair_logs_statut_index` (`statut`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Index pour la table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicles_immatriculation_unique` (`immatriculation`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `fuel_entries`
--
ALTER TABLE `fuel_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `repair_logs`
--
ALTER TABLE `repair_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `fuel_entries`
--
ALTER TABLE `fuel_entries`
  ADD CONSTRAINT `fuel_entries_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `repair_logs`
--
ALTER TABLE `repair_logs`
  ADD CONSTRAINT `repair_logs_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
