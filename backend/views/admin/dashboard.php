<?php
$title = 'Painel Administrativo - CryptoArb Pro';
$currentPage = 'admin';
?>

<div class="fade-in admin-container">
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-icon">👥</span>
                <span class="stat-card-change positive">+12%</span>
            </div>
            <div class="stat-card-content">
                <h3>Total de Usuários</h3>
                <p><?php echo number_format($stats['total_users']); ?></p>
                <small>Usuários registrados</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-icon">✅</span>
                <span class="stat-card-change positive">+8%</span>
            </div>
            <div class="stat-card-content">
                <h3>Usuários Ativos</h3>
                <p><?php echo number_format($stats['active_users']); ?></p>
                <small>Esta semana</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-icon">💰</span>
                <span class="stat-card-change positive">+15%</span>
            </div>
            <div class="stat-card-content">
                <h3>Volume Total</h3>
                <p>$<?php echo number_format($stats['total_volume']); ?></p>
                <small>Este mês</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-icon">📈</span>
                <span class="stat-card-change positive">+22%</span>
            </div>
            <div class="stat-card-content">
                <h3>Lucro Total</h3>
                <p>$<?php echo number_format($stats['total_profit']); ?></p>
                <small>Este mês</small>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Status do Sistema</h2>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div class="status-indicator online">
                    <div class="status-dot"></div>
                    <span style="color: #f1f5f9;">Sistema Principal</span>
                    <span class="text-success">ONLINE</span>
                </div>
                <div class="status-indicator online">
                    <div class="status-dot"></div>
                    <span style="color: #f1f5f9;">API Externa</span>
                    <span class="text-success">ONLINE</span>
                </div>
                <div class="status-indicator online">
                    <div class="status-dot"></div>
                    <span style="color: #f1f5f9;">Banco de Dados</span>
                    <span class="text-success">ONLINE</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Atividade Recente</h2>
        </div>
        <div class="card-body">
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <?php foreach ($recentActivity as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div class="activity-content">
                            <p class="activity-message"><?php echo htmlspecialchars($activity['message']); ?></p>
                        </div>
                        <span class="activity-time"><?php echo htmlspecialchars($activity['time']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Ações Rápidas</h2>
        </div>
        <div class="card-body">
            <div class="quick-actions">
                <a href="/admin/users" class="quick-action">
                    <span class="quick-action-icon">👥</span>
                    <span class="quick-action-text">Gerenciar Usuários</span>
                </a>
                <a href="/admin/operations" class="quick-action">
                    <span class="quick-action-icon">📊</span>
                    <span class="quick-action-text">Ver Operações</span>
                </a>
                <a href="/admin/cryptos" class="quick-action">
                    <span class="quick-action-icon">₿</span>
                    <span class="quick-action-text">Criptomoedas</span>
                </a>
                <a href="/admin/settings" class="quick-action">
                    <span class="quick-action-icon">⚙️</span>
                    <span class="quick-action-text">Configurações</span>
                </a>
            </div>
        </div>
    </div>
</div>