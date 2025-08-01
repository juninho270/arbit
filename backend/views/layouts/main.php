<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'CryptoArb Pro'); ?></title>
    <link rel="stylesheet" href="/api/assets/css/style.css">
    <link rel="icon" type="image/svg+xml" href="/api/assets/favicon.ico">
</head>
<body>
    <!-- Mobile Overlay -->
    <div class="mobile-overlay"></div>
    
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Header -->
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <div class="sidebar-logo-icon">₿</div>
                    <span class="sidebar-logo-text">CryptoArb</span>
                </div>
            </div>

            <!-- User Info -->
            <div class="sidebar-user">
                <div class="sidebar-user-info">
                    <div class="sidebar-user-avatar">
                        <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
                    </div>
                    <div class="sidebar-user-details">
                        <h3><?php echo htmlspecialchars($user['name'] ?? 'Usuário'); ?></h3>
                        <p><?php echo htmlspecialchars($user['role'] ?? 'user'); ?></p>
                    </div>
                </div>
                
                <div class="sidebar-balance">
                    <p>Saldo Total</p>
                    <p id="total-balance">$<?php echo number_format(($user['balance'] ?? 0) + ($user['bot_balance'] ?? 0), 2, ',', '.'); ?></p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="sidebar-nav">
                <ul>
                    <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                        <li>
                            <a href="/admin" class="<?php echo ($currentPage ?? '') === 'admin' ? 'active' : ''; ?>">
                                <span class="icon">🛡️</span>
                                <span>Painel Admin</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="/dashboard" class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">
                                <span class="icon">📊</span>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="/market" class="<?php echo ($currentPage ?? '') === 'market' ? 'active' : ''; ?>">
                                <span class="icon">📈</span>
                                <span>Mercado</span>
                            </a>
                        </li>
                        <li>
                            <a href="/arbitrage" class="<?php echo ($currentPage ?? '') === 'arbitrage' ? 'active' : ''; ?>">
                                <span class="icon">🎯</span>
                                <span>Arbitragem</span>
                            </a>
                        </li>
                        <li>
                            <a href="/bot" class="<?php echo ($currentPage ?? '') === 'bot' ? 'active' : ''; ?>">
                                <span class="icon">🤖</span>
                                <span>Bot Trading</span>
                            </a>
                        </li>
                        <li>
                            <a href="/investments" class="<?php echo ($currentPage ?? '') === 'investments' ? 'active' : ''; ?>">
                                <span class="icon">🏦</span>
                                <span>Investimentos</span>
                            </a>
                        </li>
                        <li>
                            <a href="/settings" class="<?php echo ($currentPage ?? '') === 'settings' ? 'active' : ''; ?>">
                                <span class="icon">⚙️</span>
                                <span>Configurações</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- Logout -->
            <div class="sidebar-logout">
                <button onclick="logout()">
                    <span class="icon">🚪</span>
                    <span>Sair</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-content">
                    <div class="header-left">
                        <!-- Mobile Menu Button -->
                        <button class="mobile-menu-btn">
                            <span class="icon">☰</span>
                        </button>
                        
                        <?php if (($user['role'] ?? 'user') !== 'admin'): ?>
                        <div class="header-balance">
                            <div class="header-balance-item">
                                <span class="icon">💰</span>
                                <div>
                                    <p>Saldo Principal</p>
                                    <p id="main-balance">$<?php echo number_format($user['balance'] ?? 0, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                            
                            <div class="header-balance-item">
                                <span class="icon">📈</span>
                                <div>
                                    <p>Saldo Bot</p>
                                    <p id="bot-balance">$<?php echo number_format($user['bot_balance'] ?? 0, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="header-status">
                            <div class="status-dot"></div>
                            <span>Sistema Online</span>
                        </div>
                    </div>

                    <div class="header-right">
                        <button class="notification-btn">
                            <span class="icon">🔔</span>
                            <span class="notification-badge">3</span>
                        </button>
                        
                        <div class="header-user">
                            <div class="header-user-avatar">
                                <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
                            </div>
                            <span><?php echo htmlspecialchars($user['name'] ?? 'Usuário'); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="page-content">
                <?php echo $content; ?>
            </div>
        </main>
    </div>

    <script src="/api/assets/js/app.js"></script>
    <?php if (isset($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script src="<?php echo htmlspecialchars($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>