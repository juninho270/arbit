<?php
$title = 'Painel Administrativo - CryptoArb Pro';
$currentPage = 'admin';
?>

<div class="fade-in">
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
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="status-dot"></div>
                    <span style="color: #E6E6E6;">Sistema Principal</span>
                    <span class="text-success">ONLINE</span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="status-dot"></div>
                    <span style="color: #E6E6E6;">API Externa</span>
                    <span class="text-success">ONLINE</span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="status-dot"></div>
                    <span style="color: #E6E6E6;">Banco de Dados</span>
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
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <?php foreach ($recentActivity as $activity): ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background-color: #0D0D0D; border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 8px; height: 8px; background-color: #32FF7E; border-radius: 50%;"></div>
                            <span style="color: #E6E6E6;"><?php echo htmlspecialchars($activity['message']); ?></span>
                        </div>
                        <span style="color: #A6A6A6; font-size: 14px;"><?php echo htmlspecialchars($activity['time']); ?></span>
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
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <a href="/admin/users" class="btn btn-primary">
                    <span>👥</span>
                    <span>Gerenciar Usuários</span>
                </a>
                <a href="/admin/operations" class="btn btn-secondary">
                    <span>📊</span>
                    <span>Ver Operações</span>
                </a>
                <a href="/admin/cryptos" class="btn btn-secondary">
                    <span>₿</span>
                    <span>Criptomoedas</span>
                </a>
                <a href="/admin/settings" class="btn btn-secondary">
                    <span>⚙️</span>
                    <span>Configurações</span>
                </a>
            </div>
        </div>
    </div>
</div>