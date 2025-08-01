<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CryptoArb Pro</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        
        .login-card {
            max-width: 400px;
            width: 100%;
            background-color: #1A1A1A;
            border: 1px solid #444;
            border-radius: 8px;
            padding: 32px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .login-header h1 {
            color: #E6E6E6;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .login-header p {
            color: #A6A6A6;
        }
        
        .login-form {
            space-y: 24px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-group label {
            display: block;
            color: #A6A6A6;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #A6A6A6;
            font-size: 18px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            background-color: #0D0D0D;
            border: 1px solid #444;
            border-radius: 8px;
            color: #E6E6E6;
            font-size: 14px;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #2188B6;
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #A6A6A6;
            cursor: pointer;
            font-size: 18px;
        }
        
        .password-toggle:hover {
            color: #E6E6E6;
        }
        
        .login-button {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            background-color: #2188B6;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .login-button:hover {
            background-color: #1a6b8f;
        }
        
        .login-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .error-message {
            color: #FF4D4D;
            font-size: 14px;
            text-align: center;
            margin-bottom: 16px;
        }
        
        .test-credentials {
            text-align: center;
            color: #A6A6A6;
            font-size: 14px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #444;
        }
        
        .test-credentials p {
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>CryptoArb Pro</h1>
                <p>Faça login em sua conta</p>
            </div>
            
            <form class="login-form" method="POST" action="/login" data-validate>
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <span class="input-icon">📧</span>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            class="form-input"
                            placeholder="seu@email.com"
                            value="<?php echo htmlspecialchars($email ?? ''); ?>"
                            required
                        />
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <div class="input-group">
                        <span class="input-icon">🔒</span>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="form-input"
                            placeholder="Sua senha"
                            required
                        />
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            👁️
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-button">
                    <span>Entrar</span>
                </button>
            </form>

            <div class="test-credentials">
                <p><strong>Credenciais de teste:</strong></p>
                <p>Email: admin@admin.com (Admin) ou user@user.com (Usuário)</p>
                <p>Senha: password</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = '👁️';
            }
        }

        // Auto-fill para teste
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const testUser = urlParams.get('test');
            
            if (testUser === 'admin') {
                document.getElementById('email').value = 'admin@admin.com';
                document.getElementById('password').value = 'password';
            } else if (testUser === 'user') {
                document.getElementById('email').value = 'user@user.com';
                document.getElementById('password').value = 'password';
            }
        });
    </script>
    <script src="/assets/js/app.js"></script>
</body>
</html>