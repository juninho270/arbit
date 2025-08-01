<?php
$title = 'Dashboard - CryptoArb Pro';
$currentPage = 'dashboard';
?>

<div class="fade-in dashboard-container">
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-icon">💰</span>
                <span class="stat-card-change positive">+5.2%</span>
            </div>
            <div class="stat-card-content">
                <h3>Saldo Total</h3>
                <p>$<?php echo number_format(($user['balance'] ?? 0) + ($user['bot_balance'] ?? 0), 2, ',', '.'); ?></p>
                <small>Saldo principal + bot</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-icon">📈</span>
                <span class="stat-card-change positive">+12.3%</span>
            </div>
            <div class="stat-card-content">
                <h3>Lucro do Mês</h3>
                <p>$<?php echo number_format($monthlyProfit ?? 1245.80, 2, ',', '.'); ?></p>
                <small>Arbitragem + investimentos</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-icon">🎯</span>
                <span class="stat-card-change positive">+8</span>
            </div>
            <div class="stat-card-content">
                <h3>Operações</h3>
                <p><?php echo $totalOperations ?? 0; ?></p>
                <small>Total realizadas</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-icon">🤖</span>
                <span class="stat-card-change positive">Ativo</span>
            </div>
            <div class="stat-card-content">
                <h3>Bot Ativo</h3>
                <p>5h 32m</p>
                <small>Tempo de execução hoje</small>
            </div>
        </div>
    </div>

    <!-- Charts and Operations -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
        <!-- Performance Chart -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Performance (Últimos 30 Dias)</h2>
            </div>
            <div class="card-body" style="height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="performanceChart" width="400" height="300"></canvas>
            </div>
            <div class="card-body" style="display: flex; justify-content: center; gap: 24px; border-top: 1px solid #334155;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 12px; height: 12px; background-color: #32FF7E; border-radius: 50%;"></div>
                    <span style="color: #94a3b8; font-size: 14px;">Total</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 12px; height: 12px; background-color: #2188B6; border-radius: 50%;"></div>
                    <span style="color: #94a3b8; font-size: 14px;">Manual</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 12px; height: 12px; background-color: #FFD166; border-radius: 50%;"></div>
                    <span style="color: #94a3b8; font-size: 14px;">Bot</span>
                </div>
            </div>
        </div>

        <!-- Recent Operations -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Operações Recentes</h2>
            </div>
            <div class="card-body">
                <?php if (empty($recentOperations)): ?>
                    <div style="text-align: center; padding: 48px 0;">
                        <div style="font-size: 48px; margin-bottom: 16px;">📈</div>
                        <p style="color: #94a3b8;">Nenhuma operação realizada ainda</p>
                    </div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php foreach ($recentOperations as $operation): ?>
                            <div style="display: flex; align-items: center; gap: 16px; padding: 16px; background-color: #0f172a; border: 1px solid #334155; border-radius: 8px;">
                                <div style="flex-shrink: 0;">
                                    <?php if ($operation['type'] === 'bot'): ?>
                                        <span style="color: #3b82f6; font-size: 20px;">🤖</span>
                                    <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 20px;">👤</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                        <p style="color: #f1f5f9; font-weight: 500; margin: 0;"><?php echo htmlspecialchars($operation['cryptocurrency']); ?></p>
                                        <span class="badge badge-<?php echo $operation['status'] === 'completed' ? 'success' : ($operation['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                            <?php 
                                            echo $operation['status'] === 'completed' ? 'Concluída' : 
                                                 ($operation['status'] === 'pending' ? 'Pendente' : 'Falhou'); 
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <div style="display: flex; align-items: center; gap: 16px; font-size: 14px; color: #94a3b8;">
                                        <span>Valor: $<?php echo number_format($operation['amount'], 2); ?></span>
                                        <span class="text-success">
                                            Lucro: $<?php echo number_format($operation['profit'], 2); ?> (<?php echo number_format($operation['profit_percentage'], 2); ?>%)
                                        </span>
                                        <div style="display: flex; align-items: center; gap: 4px;">
                                            <span>🕒</span>
                                            <span><?php echo date('d/m H:i', strtotime($operation['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if (!empty($operation['transaction_hash'])): ?>
                                    <button 
                                        onclick="window.open('https://bscscan.com/tx/<?php echo htmlspecialchars($operation['transaction_hash']); ?>', '_blank')"
                                        style="background: none; border: none; color: #3b82f6; cursor: pointer; padding: 8px;"
                                        title="Ver no Explorer"
                                    >
                                        🔗
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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
                <a href="/arbitrage" class="quick-action" style="border-color: #3b82f6;">
                    <span class="quick-action-icon">🎯</span>
                    <span class="quick-action-text">Nova Arbitragem</span>
                </a>
                <a href="/bot" class="quick-action">
                    <span class="quick-action-icon">🤖</span>
                    <span class="quick-action-text">Configurar Bot</span>
                </a>
                <a href="/investments" class="quick-action" style="border-color: #10b981;">
                    <span class="quick-action-icon">🏦</span>
                    <span class="quick-action-text">Investir</span>
                </a>
                <a href="/market" class="quick-action">
                    <span class="quick-action-icon">📊</span>
                    <span class="quick-action-text">Ver Mercado</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>