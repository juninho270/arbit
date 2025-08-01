// CryptoArb Pro - JavaScript Application

class CryptoApp {
    constructor() {
        this.init();
    }

    init() {
        this.setupMobileMenu();
        this.setupNotifications();
        this.setupForms();
        this.setupTables();
        this.startAutoRefresh();
    }

    setupMobileMenu() {
        const menuBtn = document.querySelector('.mobile-menu-btn');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.mobile-overlay');
        const mainContent = document.querySelector('.main-content');

        if (menuBtn && sidebar) {
            menuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay?.classList.toggle('active');
                mainContent?.classList.toggle('sidebar-open');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar?.classList.remove('open');
                overlay.classList.remove('active');
                mainContent?.classList.remove('sidebar-open');
            });
        }

        // Close sidebar on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar?.classList.remove('open');
                overlay?.classList.remove('active');
                mainContent?.classList.remove('sidebar-open');
            }
        });
    }

    setupNotifications() {
        // Setup notification system
        this.notifications = [];
    }

    showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;

        // Add to page
        let container = document.querySelector('.notification-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'notification-container';
            document.body.appendChild(container);
        }

        container.appendChild(notification);

        // Auto remove
        setTimeout(() => {
            notification.remove();
        }, duration);

        // Manual close
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });
    }

    setupForms() {
        // Form validation and enhancement
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
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showFieldError(input, 'Este campo é obrigatório');
                isValid = false;
            } else {
                this.clearFieldError(input);
            }
        });

        return isValid;
    }

    showFieldError(input, message) {
        this.clearFieldError(input);
        
        const error = document.createElement('div');
        error.className = 'field-error';
        error.textContent = message;
        error.style.color = '#FF4D4D';
        error.style.fontSize = '12px';
        error.style.marginTop = '4px';
        
        input.parentNode.appendChild(error);
        input.style.borderColor = '#FF4D4D';
    }

    clearFieldError(input) {
        const error = input.parentNode.querySelector('.field-error');
        if (error) {
            error.remove();
        }
        input.style.borderColor = '';
    }

    setupTables() {
        // Enhanced table functionality
        const tables = document.querySelectorAll('.table');
        
        tables.forEach(table => {
            this.makeTableResponsive(table);
        });
    }

    makeTableResponsive(table) {
        const wrapper = document.createElement('div');
        wrapper.style.overflowX = 'auto';
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
    }

    startAutoRefresh() {
        // Auto-refresh certain elements every 30 seconds
        setInterval(() => {
            this.refreshDynamicContent();
        }, 30000);
    }

    refreshDynamicContent() {
        // Refresh crypto prices, balances, etc.
        const priceElements = document.querySelectorAll('[data-crypto-price]');
        const balanceElements = document.querySelectorAll('[data-balance]');
        
        // This would typically make AJAX calls to update content
        // For now, we'll just add a subtle animation to show it's refreshing
        [...priceElements, ...balanceElements].forEach(element => {
            element.style.opacity = '0.7';
            setTimeout(() => {
                element.style.opacity = '1';
            }, 500);
        });
    }

    // Utility functions
    formatCurrency(amount, currency = 'USD') {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: currency
        }).format(amount);
    }

    formatPercentage(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'percent',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value / 100);
    }

    formatDate(date) {
        return new Intl.DateTimeFormat('pt-BR', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(date));
    }
}

// Logout function
function logout() {
    if (confirm('Tem certeza que deseja sair?')) {
        window.location.href = '/logout';
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.cryptoApp = new CryptoApp();
});

// Global error handler
window.addEventListener('error', (e) => {
    console.error('JavaScript Error:', e.error);
    
    if (window.cryptoApp) {
        window.cryptoApp.showNotification(
            'Ocorreu um erro inesperado. Recarregue a página se necessário.',
            'danger'
        );
    }
});

// Service Worker registration (for future PWA features)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}