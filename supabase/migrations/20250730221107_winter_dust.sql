-- =====================================================
-- CryptoArb Pro - Database Setup Script
-- =====================================================
-- Este script cria todas as tabelas e insere os dados iniciais
-- Execute este script no seu banco de dados MySQL

-- =====================================================
-- 1. CRIAÇÃO DAS TABELAS
-- =====================================================

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

-- =====================================================
-- 2. INSERÇÃO DOS DADOS INICIAIS (SEEDERS)
-- =====================================================

-- Usuários padrão
INSERT INTO `users` (`name`, `email`, `password`, `balance`, `bot_balance`, `role`, `status`, `created_at`, `updated_at`) VALUES
('Administrador', 'admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 50000.00, 25000.00, 'admin', 'active', NOW(), NOW()),
('Usuário Teste', 'user@user.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 10000.00, 5000.00, 'user', 'active', NOW(), NOW()),
('João Silva', 'joao@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5420.50, 2100.00, 'user', 'active', NOW(), NOW()),
('Maria Santos', 'maria@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 8750.25, 0.00, 'user', 'active', NOW(), NOW()),
('Pedro Costa', 'pedro@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1250.00, 500.00, 'user', 'suspended', NOW(), NOW());

-- Planos de investimento
INSERT INTO `investment_plans` (`name`, `description`, `min_amount`, `max_amount`, `daily_return`, `duration`, `total_return`, `risk`, `is_active`, `created_at`, `updated_at`) VALUES
('Plano Iniciante', 'Ideal para iniciantes no mundo dos investimentos', 100.00, 1000.00, 2.5000, 30, 75.0000, 'low', 1, NOW(), NOW()),
('Plano Intermediário', 'Para investidores com experiência moderada', 1000.00, 5000.00, 3.8000, 45, 171.0000, 'medium', 1, NOW(), NOW()),
('Plano Avançado', 'Para investidores experientes', 5000.00, 20000.00, 5.2000, 60, 312.0000, 'high', 1, NOW(), NOW());

-- Configurações do sistema
INSERT INTO `system_settings` (`key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
('arbitrage_enabled', '1', 'boolean', 'Permitir operações de arbitragem', NOW(), NOW()),
('bot_enabled', '1', 'boolean', 'Permitir ativação de bots', NOW(), NOW()),
('min_arbitrage_amount', '100', 'decimal', 'Valor mínimo para arbitragem', NOW(), NOW()),
('max_arbitrage_amount', '10000', 'decimal', 'Valor máximo para arbitragem', NOW(), NOW()),
('arbitrage_fee', '2.5', 'decimal', 'Taxa de arbitragem em porcentagem', NOW(), NOW()),
('bot_activation_fee', '50', 'decimal', 'Taxa de ativação do bot', NOW(), NOW()),
('maintenance_mode', '0', 'boolean', 'Modo de manutenção', NOW(), NOW());

-- Operações de arbitragem de exemplo
INSERT INTO `arbitrage_operations` (`user_id`, `type`, `cryptocurrency`, `amount`, `buy_price`, `sell_price`, `profit`, `profit_percentage`, `status`, `transaction_hash`, `chain`, `execution_time`, `completed_at`, `created_at`, `updated_at`) VALUES
(2, 'manual', 'Bitcoin', 1000.00, 45000.00000000, 47250.00000000, 50.00, 5.0000, 'completed', '0x1234567890abcdef1234567890abcdef12345678', 'bsc', 4500, NOW(), NOW(), NOW()),
(3, 'manual', 'Ethereum', 500.00, 3000.00000000, 3150.00000000, 25.00, 5.0000, 'completed', '0xabcdef1234567890abcdef1234567890abcdef12', 'eth', 3200, NOW(), NOW(), NOW()),
(2, 'bot', 'Cardano', 200.00, 0.50000000, 0.52500000, 10.00, 5.0000, 'completed', '0x567890abcdef1234567890abcdef1234567890ab', 'bsc', 2800, NOW(), NOW(), NOW());

-- Configurações de bot de exemplo
INSERT INTO `bot_settings` (`user_id`, `is_active`, `min_profit`, `max_amount`, `interval`, `selected_coins`, `auto_reinvest`, `stop_loss`, `created_at`, `updated_at`) VALUES
(2, 0, 2.0000, 1000.00, 300, '["bitcoin", "ethereum"]', 1, 5.0000, NOW(), NOW()),
(3, 1, 3.0000, 500.00, 600, '["bitcoin", "cardano"]', 0, 3.0000, NOW(), NOW());

-- Investimentos de exemplo
INSERT INTO `investments` (`user_id`, `investment_plan_id`, `amount`, `expected_return`, `current_return`, `duration`, `status`, `start_date`, `end_date`, `progress`, `created_at`, `updated_at`) VALUES
(2, 1, 500.00, 375.00, 187.50, 30, 'active', DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_ADD(NOW(), INTERVAL 15 DAY), 50.00, NOW(), NOW()),
(3, 2, 2000.00, 3420.00, 1140.00, 45, 'active', DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_ADD(NOW(), INTERVAL 30 DAY), 33.33, NOW(), NOW()),
(4, 1, 1000.00, 750.00, 750.00, 30, 'completed', DATE_SUB(NOW(), INTERVAL 35 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), 100.00, NOW(), NOW());

-- =====================================================
-- 3. VERIFICAÇÃO DOS DADOS INSERIDOS
-- =====================================================

-- Verificar usuários criados
SELECT 'USUÁRIOS CRIADOS:' as info;
SELECT id, name, email, role, status, balance, bot_balance FROM users;

-- Verificar planos de investimento
SELECT 'PLANOS DE INVESTIMENTO:' as info;
SELECT id, name, min_amount, max_amount, daily_return, duration, risk FROM investment_plans;

-- Verificar configurações do sistema
SELECT 'CONFIGURAÇÕES DO SISTEMA:' as info;
SELECT `key`, `value`, `type`, `description` FROM system_settings;

-- Verificar operações de arbitragem
SELECT 'OPERAÇÕES DE ARBITRAGEM:' as info;
SELECT id, user_id, type, cryptocurrency, amount, profit, status FROM arbitrage_operations;

-- =====================================================
-- SCRIPT CONCLUÍDO COM SUCESSO!
-- =====================================================
-- 
-- CREDENCIAIS DE ACESSO:
-- 
-- ADMIN:
-- Email: admin@admin.com
-- Senha: password
-- 
-- USUÁRIO TESTE:
-- Email: user@user.com  
-- Senha: password
-- 
-- OUTROS USUÁRIOS DE TESTE:
-- Email: joao@email.com, maria@email.com, pedro@email.com
-- Senha: password (para todos)
-- 
-- =====================================================