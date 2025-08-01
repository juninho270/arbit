<?php
$title = 'Dashboard - CryptoArb Pro';
$currentPage = 'dashboard';
?>

<div class="fade-in">
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
            <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="performanceChart" width="400" height="300"></canvas>
            </div>
            <div style="display: flex; justify-content: center; gap: 24px; margin-top: 16px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 12px; height: 12px; background-color: #32FF7E; border-radius: 50%;"></div>
                    <span style="color: #A6A6A6; font-size: 14px;">Total</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 12px; height: 12px; background-color: #2188B6; border-radius: 50%;"></div>
                    <span style="color: #A6A6A6; font-size: 14px;">Manual</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 12px; height: 12px; background-color: #FFD166; border-radius: 50%;"></div>
                    <span style="color: #A6A6A6; font-size: 14px;">Bot</span>
                </div>
            </div>
        </div>

        <!-- Recent Operations -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Operações Recentes</h2>
            </div>
            <?php if (empty($recentOperations)): ?>
                <div style="text-align: center; padding: 48px 0;">
                    <div style="font-size: 48px; margin-bottom: 16px;">📈</div>
                    <p style="color: #A6A6A6;">Nenhuma operação realizada ainda</p>
                </div>
            <?php else: ?>
                <div style="space-y: 16px;">
                    <?php foreach ($recentOperations as $operation): ?>
                        <div style="display: flex; align-items: center; gap: 16px; padding: 16px; background-color: #0D0D0D; border-radius: 8px;">
                            <div style="flex-shrink: 0;">
                                <?php if ($operation['type'] === 'bot'): ?>
                                    <span style="color: #2188B6; font-size: 20px;">🤖</span>
                                <?php else: ?>
                                    <span style="color: #A6A6A6; font-size: 20px;">👤</span>
                                <?php endif; ?>
                            </div>
                            
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                    <p style="color: #E6E6E6; font-weight: 500;"><?php echo htmlspecialchars($operation['cryptocurrency']); ?></p>
                                    <span class="badge badge-<?php echo $operation['status'] === 'completed' ? 'success' : ($operation['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                        <?php 
                                        echo $operation['status'] === 'completed' ? 'Concluída' : 
                                             ($operation['status'] === 'pending' ? 'Pendente' : 'Falhou'); 
                                        ?>
                                    </span>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 16px; font-size: 14px; color: #A6A6A6;">
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
                                    onclick="window.open('<?php echo $this->getExplorerUrl($operation['transaction_hash'], $operation['chain'] ?? 'bsc'); ?>', '_blank')"
                                    style="background: none; border: none; color: #2188B6; cursor: pointer; padding: 8px;"
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

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Ações Rápidas</h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <a href="/arbitrage" class="btn btn-primary">
                <span>🎯</span>
                <span>Nova Arbitragem</span>
            </a>
            <a href="/bot" class="btn btn-secondary">
                <span>🤖</span>
                <span>Configurar Bot</span>
            </a>
            <a href="/investments" class="btn btn-success">
                <span>🏦</span>
                <span>Investir</span>
            </a>
            <a href="/market" class="btn btn-secondary">
                <span>📊</span>
                <span>Ver Mercado</span>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>