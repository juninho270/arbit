// CryptoArb Pro - JavaScript principal
class CryptoArbApp {
    constructor() {
        this.init();
    }

    init() {
        this.setupMobileMenu();
        this.setupNotifications();
        this.setupFormValidation();
        this.setupCharts();
        this.startRealTimeUpdates();
    }

    // Menu mobile
    setupMobileMenu() {
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.mobile-overlay');

        if (mobileMenuBtn && sidebar) {
            mobileMenuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
                if (overlay) {
                    overlay.classList.toggle('active');
                }
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            });
        }

        // Fechar menu ao redimensionar
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('mobile-open');
                if (overlay) {
                    overlay.classList.remove('active');
                }
            }
        });
    }

    // Sistema de notificações
    setupNotifications() {
        this.notifications = [];
        this.createNotificationContainer();
    }

    createNotificationContainer() {
        if (!document.querySelector('.notifications-container')) {
            const container = document.createElement('div');
            container.className = 'notifications-container';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
                max-width: 400px;
            `;
            document.body.appendChild(container);
        }
    }

    showNotification(message, type = 'info', duration = 5000) {
        const container = document.querySelector('.notifications-container');
        const notification = document.createElement('div');
        
        const colors = {
            success: '#32FF7E',
            error: '#FF4D4D',
            warning: '#FFD166',
            info: '#2188B6'
        };

        notification.className = 'notification fade-in';
        notification.style.cssText = `
            background-color: #1A1A1A;
            border: 1px solid ${colors[type]};
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            color: #E6E6E6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        `;

        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 4px; height: 100%; background-color: ${colors[type]}; position: absolute; left: 0; top: 0;"></div>
                <div style="flex: 1;">${message}</div>
                <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: #A6A6A6; cursor: pointer; font-size: 18px;">&times;</button>
            </div>
        `;

        container.appendChild(notification);

        // Auto remover
        if (duration > 0) {
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, duration);
        }
    }

    // Validação de formulários
    setupFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.showFieldError(field, 'Este campo é obrigatório');
                isValid = false;
            } else {
                this.clearFieldError(field);
            }
        });

        // Validação de email
        const emailFields = form.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => {
            if (field.value && !this.isValidEmail(field.value)) {
                this.showFieldError(field, 'Email inválido');
                isValid = false;
            }
        });

        return isValid;
    }

    showFieldError(field, message) {
        this.clearFieldError(field);
        
        const error = document.createElement('div');
        error.className = 'field-error';
        error.style.cssText = 'color: #FF4D4D; font-size: 12px; margin-top: 4px;';
        error.textContent = message;
        
        field.style.borderColor = '#FF4D4D';
        field.parentElement.appendChild(error);
    }

    clearFieldError(field) {
        const error = field.parentElement.querySelector('.field-error');
        if (error) {
            error.remove();
        }
        field.style.borderColor = '#444';
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Configuração de gráficos (usando Chart.js se disponível)
    setupCharts() {
        if (typeof Chart !== 'undefined') {
            this.initPerformanceChart();
        }
    }

    initPerformanceChart() {
        const canvas = document.getElementById('performanceChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        // Dados de exemplo (em produção, viriam do backend)
        const data = this.generatePerformanceData();

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Lucro Total',
                    data: data.profit,
                    borderColor: '#32FF7E',
                    backgroundColor: 'rgba(50, 255, 126, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Manual',
                    data: data.manual,
                    borderColor: '#2188B6',
                    backgroundColor: 'rgba(33, 136, 182, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Bot',
                    data: data.bot,
                    borderColor: '#FFD166',
                    backgroundColor: 'rgba(255, 209, 102, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#E6E6E6'
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#A6A6A6'
                        },
                        grid: {
                            color: '#444'
                        }
                    },
                    y: {
                        ticks: {
                            color: '#A6A6A6',
                            callback: function(value) {
                                return '$' + value.toFixed(2);
                            }
                        },
                        grid: {
                            color: '#444'
                        }
                    }
                }
            }
        });
    }

    generatePerformanceData() {
        const labels = [];
        const profit = [];
        const manual = [];
        const bot = [];

        for (let i = 29; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            labels.push(date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' }));
            
            const profitValue = Math.random() * 100 + 50;
            const manualValue = Math.random() * 50 + 20;
            const botValue = Math.random() * 50 + 30;
            
            profit.push(profitValue);
            manual.push(manualValue);
            bot.push(botValue);
        }

        return { labels, profit, manual, bot };
    }

    // Atualizações em tempo real
    startRealTimeUpdates() {
        // Atualizar preços a cada 30 segundos
        setInterval(() => {
            this.updateCryptoPrices();
        }, 30000);

        // Atualizar saldos a cada 60 segundos
        setInterval(() => {
            this.updateBalances();
        }, 60000);
    }

    async updateCryptoPrices() {
        try {
            const response = await fetch('/api/cryptocurrencies');
            const cryptos = await response.json();
            
            // Atualizar tabela de criptomoedas se existir
            const cryptoTable = document.querySelector('#crypto-table tbody');
            if (cryptoTable && Array.isArray(cryptos)) {
                this.updateCryptoTable(cryptoTable, cryptos);
            }
        } catch (error) {
            console.error('Erro ao atualizar preços:', error);
        }
    }

    updateCryptoTable(tbody, cryptos) {
        const rows = tbody.querySelectorAll('tr');
        
        rows.forEach((row, index) => {
            if (cryptos[index]) {
                const crypto = cryptos[index];
                const priceCell = row.querySelector('.crypto-price');
                const changeCell = row.querySelector('.crypto-change');
                
                if (priceCell) {
                    priceCell.textContent = this.formatPrice(crypto.current_price);
                }
                
                if (changeCell) {
                    const change = crypto.price_change_percentage_24h;
                    changeCell.textContent = (change > 0 ? '+' : '') + change.toFixed(2) + '%';
                    changeCell.className = 'crypto-change ' + (change > 0 ? 'text-success' : 'text-danger');
                }
            }
        });
    }

    async updateBalances() {
        try {
            const response = await fetch('/api/me');
            const user = await response.json();
            
            // Atualizar saldos no header
            const mainBalance = document.querySelector('#main-balance');
            const botBalance = document.querySelector('#bot-balance');
            const totalBalance = document.querySelector('#total-balance');
            
            if (mainBalance) {
                mainBalance.textContent = this.formatCurrency(user.balance);
            }
            
            if (botBalance) {
                botBalance.textContent = this.formatCurrency(user.bot_balance);
            }
            
            if (totalBalance) {
                totalBalance.textContent = this.formatCurrency(user.balance + user.bot_balance);
            }
        } catch (error) {
            console.error('Erro ao atualizar saldos:', error);
        }
    }

    // Utilitários
    formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2
        }).format(value).replace('US$', '$');
    }

    formatPrice(price) {
        if (price < 1) {
            return '$' + price.toFixed(6);
        }
        return '$' + price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // API helpers
    async apiCall(endpoint, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };

        const token = localStorage.getItem('auth_token');
        if (token) {
            defaultOptions.headers['Authorization'] = `Bearer ${token}`;
        }

        try {
            const response = await fetch(endpoint, { ...defaultOptions, ...options });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return await response.json();
        } catch (error) {
            this.showNotification('Erro na comunicação com o servidor', 'error');
            throw error;
        }
    }
}

// Inicializar aplicação quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.cryptoApp = new CryptoArbApp();
});

// Funções globais para uso nos templates
window.showNotification = (message, type, duration) => {
    if (window.cryptoApp) {
        window.cryptoApp.showNotification(message, type, duration);
    }
};

window.formatCurrency = (value) => {
    if (window.cryptoApp) {
        return window.cryptoApp.formatCurrency(value);
    }
    return '$' + value.toFixed(2);
};

// Logout function
window.logout = async () => {
    try {
        await fetch('/api/logout', { method: 'POST' });
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        window.location.href = '/login';
    } catch (error) {
        console.error('Erro no logout:', error);
        // Mesmo com erro, limpar dados locais e redirecionar
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        window.location.href = '/login';
    }
};