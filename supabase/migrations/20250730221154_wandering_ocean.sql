-- =====================================================
-- CryptoArb Pro - Quick Setup (Apenas Tabelas)
-- =====================================================
-- Use este script se você quiser criar apenas as tabelas
-- sem os dados de exemplo

-- Tabela: users
CREATE TABLE IF NOT EXISTS `users` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) NOT NULL,
    `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
    `bot_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
    `role` enum('user','admin') NOT NULL DEFAULT 'user',
    `status` enum('active','suspended','pending') NOT NULL DEFAULT 'active',
    `last_login` timestamp NULL DEFAULT NULL,
    `remember_token` varchar(100) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: cryptocurrencies
CREATE TABLE IF NOT EXISTS `cryptocurrencies` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `coin_id` varchar(255) NOT NULL,
    `symbol` varchar(255) NOT NULL,
    `name` varchar(255) NOT NULL,
    `current_price` decimal(20,8) DEFAULT NULL,
    `price_change_percentage_24h` decimal(10,4) DEFAULT NULL,
    `market_cap` bigint(20) DEFAULT NULL,
    `volume_24h` bigint(20) DEFAULT NULL,
    `image` varchar(255) DEFAULT NULL,
    `contract_address` varchar(255) DEFAULT NULL,
    `is_arbitrage_enabled` tinyint(1) NOT NULL DEFAULT 1,
    `deactivation_reason` text DEFAULT NULL,
    `last_updated` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `cryptocurrencies_coin_id_unique` (`coin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: investment_plans
CREATE TABLE IF NOT EXISTS `investment_plans` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `min_amount` decimal(15,2) NOT NULL,
    `max_amount` decimal(15,2) NOT NULL,
    `daily_return` decimal(8,4) NOT NULL,
    `duration` int(11) NOT NULL,
    `total_return` decimal(8,4) NOT NULL,
    `risk` enum('low','medium','high') NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: arbitrage_operations
CREATE TABLE IF NOT EXISTS `arbitrage_operations` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `type` enum('manual','bot') NOT NULL,
    `cryptocurrency` varchar(255) NOT NULL,
    `amount` decimal(15,2) NOT NULL,
    `buy_price` decimal(20,8) NOT NULL,
    `sell_price` decimal(20,8) NOT NULL,
    `profit` decimal(15,2) NOT NULL,
    `profit_percentage` decimal(8,4) NOT NULL,
    `status` enum('pending','completed','failed','cancelled_no_hash') NOT NULL,
    `transaction_hash` varchar(255) DEFAULT NULL,
    `chain` varchar(255) DEFAULT NULL,
    `no_hash_reason` text DEFAULT NULL,
    `execution_time` int(11) NOT NULL,
    `completed_at` timestamp NULL DEFAULT NULL,
    `error_message` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `arbitrage_operations_user_id_foreign` (`user_id`),
    CONSTRAINT `arbitrage_operations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: bot_settings
CREATE TABLE IF NOT EXISTS `bot_settings` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 0,
    `min_profit` decimal(8,4) NOT NULL DEFAULT 2.0000,
    `max_amount` decimal(15,2) NOT NULL DEFAULT 1000.00,
    `interval` int(11) NOT NULL DEFAULT 300,
    `selected_coins` json DEFAULT NULL,
    `auto_reinvest` tinyint(1) NOT NULL DEFAULT 1,
    `stop_loss` decimal(8,4) NOT NULL DEFAULT 5.0000,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `bot_settings_user_id_foreign` (`user_id`),
    CONSTRAINT `bot_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: investments
CREATE TABLE IF NOT EXISTS `investments` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `investment_plan_id` bigint(20) UNSIGNED NOT NULL,
    `amount` decimal(15,2) NOT NULL,
    `expected_return` decimal(15,2) NOT NULL,
    `current_return` decimal(15,2) NOT NULL DEFAULT 0.00,
    `duration` int(11) NOT NULL,
    `status` enum('active','completed','cancelled') NOT NULL,
    `start_date` timestamp NOT NULL,
    `end_date` timestamp NOT NULL,
    `progress` decimal(5,2) NOT NULL DEFAULT 0.00,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `investments_user_id_foreign` (`user_id`),
    KEY `investments_investment_plan_id_foreign` (`investment_plan_id`),
    CONSTRAINT `investments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `investments_investment_plan_id_foreign` FOREIGN KEY (`investment_plan_id`) REFERENCES `investment_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: system_settings
CREATE TABLE IF NOT EXISTS `system_settings` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` varchar(255) NOT NULL,
    `value` text NOT NULL,
    `type` varchar(255) NOT NULL DEFAULT 'string',
    `description` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `system_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;