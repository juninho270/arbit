<?php
$title = 'Arbitragem Manual - CryptoArb Pro';
$currentPage = 'arbitrage';
?>

<div class="fade-in">
    <!-- Arbitrage Form -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Arbitragem Manual</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="/arbitrage/execute" data-validate>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div class="form-group">
                        <label>Criptomoeda</label>
                        <select name="cryptocurrency" class="form-select" required onchange="updateCoinId(this)">
                            <option value="">Selecione uma criptomoeda</option>
                            <?php foreach ($cryptocurrencies as $crypto): ?>
                                <option value="<?php echo htmlspecialchars($crypto['name']); ?>" data-coin-id="<?php echo htmlspecialchars($crypto['id']); ?>" data-price="<?php echo $crypto['current_price']; ?>">
                                    <?php echo htmlspecialchars($crypto['name']); ?> (<?php echo strtoupper($crypto['symbol']); ?>) - $<?php echo number_format($crypto['current_price'], 2); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="coin_id" id="coin_id">
                    </div>

                    <div class="form-group">
                        <label>Valor (USD)</label>
                        <input type="number" name="amount" class="form-input" min="1" max="<?php echo $user['balance']; ?>" step="0.01" required>
                        <small style="color: #A6A6A6;">Saldo disponível: $<?php echo number_format($user['balance'], 2); ?></small>
                    </div>
                </div>

                <!-- Operation Preview -->
                <div id="operationPreview" style="display: none; background-color: #0D0D0D; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
                    <h4 style="color: #E6E6E6; margin-bottom: 16px;">Previsão da Operação</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px;">
                        <div>
                            <p style="color: #A6A6A6; font-size: 14px;">Preço de Compra</p>
                            <p id="buyPrice" style="color: #E6E6E6; font-family: monospace;">$0.00</p>
                        </div>
                        <div>
                            <p style="color: #A6A6A6; font-size: 14px;">Preço de Venda</p>
                            <p id="sellPrice" style="color: #E6E6E6; font-family: monospace;">$0.00</p>
                        </div>
                        <div>
                            <p style="color: #A6A6A6; font-size: 14px;">Lucro Estimado</p>
                            <p id="estimatedProfit" style="color: #32FF7E; font-family: monospace;">$2.00 - $8.00</p>
                        </div>
                        <div>
                            <p style="color: #A6A6A6; font-size: 14px;">ROI Estimado</p>
                            <p id="estimatedROI" style="color: #32FF7E; font-family: monospace;">2% - 8%</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">
                    <span>🎯</span>
                    <span>Executar Arbitragem</span>
                </button>
            </form>
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
                    <p style="color: #A6A6A6;">Nenhuma operação realizada ainda</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <?php foreach ($recentOperations as $operation): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background-color: #0D0D0D; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="flex-shrink: 0;">
                                    <?php if ($operation['type'] === 'bot'): ?>
                                        <span style="color: #2188B6; font-size: 20px;">🤖</span>
                                    <?php else: ?>
                                        <span style="color: #A6A6A6; font-size: 20px;">👤</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                        <p style="color: #E6E6E6; font-weight: 500; margin: 0;"><?php echo htmlspecialchars($operation['cryptocurrency']); ?></p>
                                        <?php
                                        $statusClass = $operation['status'] === 'completed' ? 'badge-success' : 
                                                      ($operation['status'] === 'pending' ? 'badge-warning' : 'badge-danger');
                                        $statusText = $operation['status'] === 'completed' ? 'Concluída' : 
                                                     ($operation['status'] === 'pending' ? 'Pendente' : 'Falhou');
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
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
                            </div>
                            
                            <?php if (!empty($operation['transaction_hash'])): ?>
                                <button 
                                    onclick="window.open('https://bscscan.com/tx/<?php echo htmlspecialchars($operation['transaction_hash']); ?>', '_blank')"
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
</div>

<script>
function updateCoinId(select) {
    const selectedOption = select.options[select.selectedIndex];
    const coinId = selectedOption.getAttribute('data-coin-id');
    const price = parseFloat(selectedOption.getAttribute('data-price'));
    
    document.getElementById('coin_id').value = coinId || '';
    
    if (coinId && price) {
        updateOperationPreview(price);
        document.getElementById('operationPreview').style.display = 'block';
    } else {
        document.getElementById('operationPreview').style.display = 'none';
    }
}

function updateOperationPreview(price) {
    const buyPrice = price;
    const sellPrice = price * 1.05; // 5% higher
    
    document.getElementById('buyPrice').textContent = '$' + buyPrice.toFixed(4);
    document.getElementById('sellPrice').textContent = '$' + sellPrice.toFixed(4);
}

// Show success/error messages
<?php if (isset($_GET['success'])): ?>
    <?php if ($_GET['success'] === 'operation_completed'): ?>
        window.cryptoApp?.showNotification('Operação de arbitragem concluída com sucesso! Lucro: $<?php echo number_format($_GET['profit'] ?? 0, 2); ?>', 'success');
    <?php endif; ?>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    window.cryptoApp?.showNotification('<?php echo htmlspecialchars($_GET['error']); ?>', 'danger');
<?php endif; ?>
</script>