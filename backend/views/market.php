<?php
$title = 'Mercado - CryptoArb Pro';
$currentPage = 'market';
?>

<div class="fade-in">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Mercado de Criptomoedas</h2>
            <div style="display: flex; gap: 8px;">
                <button id="filter-all" class="btn btn-primary">Todas</button>
                <button id="filter-favorites" class="btn btn-secondary">
                    <span>⭐</span>
                    <span>Favoritas</span>
                </button>
            </div>
        </div>
        
        <!-- Crypto Table -->
        <div style="overflow-x: auto;">
            <table class="table" id="crypto-table">
                <thead>
                    <tr>
                        <th>Moeda</th>
                        <th style="text-align: right;">Preço</th>
                        <th style="text-align: right;">24h</th>
                        <th style="text-align: right;">Market Cap</th>
                        <th style="text-align: right;">Volume 24h</th>
                        <th style="text-align: center;">Favorito</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cryptocurrencies)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 48px 0;">
                                <div class="spinner" style="margin: 0 auto 16px;"></div>
                                <p style="color: #A6A6A6;">Carregando preços...</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cryptocurrencies as $crypto): ?>
                            <tr data-crypto-id="<?php echo htmlspecialchars($crypto['id']); ?>">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <img 
                                            src="<?php echo htmlspecialchars($crypto['image']); ?>" 
                                            alt="<?php echo htmlspecialchars($crypto['name']); ?>"
                                            style="width: 32px; height: 32px; border-radius: 50%;"
                                            onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMyMTg4QjYiLz4KPHN2ZyB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIHZpZXdCb3g9IjAgMCAxNiAxNiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTggMTJDMTAuMjA5MSAxMiAxMiAxMC4yMDkxIDEyIDhDMTIgNS43OTA5IDEwLjIwOTEgNCA4IDRDNS43OTA5IDQgNCA1Ljc5MDkgNCA4QzQgMTAuMjA5MSA1Ljc5MDkgMTIgOCAxMloiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPgo8L3N2Zz4K'"
                                        />
                                        <div>
                                            <p style="color: #E6E6E6; font-weight: 500; margin: 0;"><?php echo htmlspecialchars($crypto['name']); ?></p>
                                            <p style="color: #A6A6A6; font-size: 14px; margin: 0;"><?php echo strtoupper(htmlspecialchars($crypto['symbol'])); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <span class="crypto-price font-mono" style="color: #E6E6E6;">
                                        $<?php echo number_format($crypto['current_price'], $crypto['current_price'] < 1 ? 6 : 2); ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div class="crypto-change <?php echo $crypto['price_change_percentage_24h'] > 0 ? 'text-success' : 'text-danger'; ?>" style="display: flex; align-items: center; justify-content: flex-end; gap: 4px;">
                                        <span><?php echo $crypto['price_change_percentage_24h'] > 0 ? '📈' : '📉'; ?></span>
                                        <span class="font-mono">
                                            <?php echo ($crypto['price_change_percentage_24h'] > 0 ? '+' : '') . number_format($crypto['price_change_percentage_24h'], 2); ?>%
                                        </span>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <span class="text-muted font-mono">
                                        $<?php echo number_format($crypto['market_cap'] ?? 0); ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <span class="text-muted font-mono">
                                        $<?php echo number_format($crypto['volume_24h'] ?? 0); ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <button 
                                        class="favorite-btn"
                                        onclick="toggleFavorite('<?php echo htmlspecialchars($crypto['id']); ?>')"
                                        style="background: none; border: none; cursor: pointer; padding: 8px; transition: transform 0.2s;"
                                        onmouseover="this.style.transform='scale(1.2)'"
                                        onmouseout="this.style.transform='scale(1)'"
                                    >
                                        <span class="favorite-icon" style="font-size: 18px; color: #A6A6A6;">♡</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Favoritos
let favorites = JSON.parse(localStorage.getItem('crypto_favorites') || '[]');

function toggleFavorite(cryptoId) {
    const index = favorites.indexOf(cryptoId);
    const button = document.querySelector(`tr[data-crypto-id="${cryptoId}"] .favorite-btn`);
    const icon = button.querySelector('.favorite-icon');
    
    if (index === -1) {
        favorites.push(cryptoId);
        icon.textContent = '❤️';
        icon.style.color = '#FF4D4D';
        showNotification('Adicionado aos favoritos', 'success');
    } else {
        favorites.splice(index, 1);
        icon.textContent = '♡';
        icon.style.color = '#A6A6A6';
        showNotification('Removido dos favoritos', 'info');
    }
    
    localStorage.setItem('crypto_favorites', JSON.stringify(favorites));
}

// Filtros
document.getElementById('filter-all').addEventListener('click', function() {
    showAllCryptos();
    setActiveFilter(this);
});

document.getElementById('filter-favorites').addEventListener('click', function() {
    showFavoritesOnly();
    setActiveFilter(this);
});

function showAllCryptos() {
    const rows = document.querySelectorAll('#crypto-table tbody tr');
    rows.forEach(row => row.style.display = '');
}

function showFavoritesOnly() {
    const rows = document.querySelectorAll('#crypto-table tbody tr');
    rows.forEach(row => {
        const cryptoId = row.dataset.cryptoId;
        if (favorites.includes(cryptoId)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function setActiveFilter(activeButton) {
    document.querySelectorAll('#filter-all, #filter-favorites').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-secondary');
    });
    activeButton.classList.remove('btn-secondary');
    activeButton.classList.add('btn-primary');
}

// Inicializar favoritos
document.addEventListener('DOMContentLoaded', function() {
    favorites.forEach(cryptoId => {
        const button = document.querySelector(`tr[data-crypto-id="${cryptoId}"] .favorite-btn`);
        if (button) {
            const icon = button.querySelector('.favorite-icon');
            icon.textContent = '❤️';
            icon.style.color = '#FF4D4D';
        }
    });
});

// Auto-refresh preços a cada 30 segundos
setInterval(() => {
    if (window.cryptoApp) {
        window.cryptoApp.updateCryptoPrices();
    }
}, 30000);
</script>