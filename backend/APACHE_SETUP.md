# Configuração do Apache para CryptoArb Pro - Domínio Principal

Este guia explica como configurar o Apache para servir a aplicação PHP MVC diretamente no domínio principal.

## 🎯 Objetivo

Configurar o Apache para que:
- `https://arbit.duckdns.org/` → Serve diretamente a aplicação PHP MVC
- `https://arbit.duckdns.org/login` → Página de login
- `https://arbit.duckdns.org/dashboard` → Dashboard do usuário
- `https://arbit.duckdns.org/assets/css/style.css` → Arquivos estáticos

## ✅ Solução

### 1. Configurar o Virtual Host

**Localização do arquivo de configuração:**
- Ubuntu/Debian: `/etc/apache2/sites-available/arbit.duckdns.org.conf`
- CentOS/RHEL: `/etc/httpd/conf.d/arbit.duckdns.org.conf`
- Plesk: Através do painel de controle

**Use o arquivo `apache-vhost-example.conf` como base** e adapte conforme sua configuração.

### 2. Comandos para Aplicar

```bash
# 1. Copiar o arquivo de configuração (ajuste o caminho)
sudo cp apache-vhost-example.conf /etc/apache2/sites-available/arbit.duckdns.org.conf

# 2. Habilitar o site (Ubuntu/Debian)
sudo a2ensite arbit.duckdns.org.conf

# 3. Desabilitar site padrão se necessário
sudo a2dissite 000-default.conf

# 4. Habilitar módulos necessários
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod expires
sudo a2enmod deflate

# 5. Testar a configuração
sudo apache2ctl configtest

# 6. Reiniciar o Apache
sudo systemctl restart apache2
```

### 3. Estrutura de Arquivos Esperada

```
/var/www/vhosts/arbit.duckdns.org/httpdocs/
└── backend/
    ├── public/                    ← DocumentRoot do Apache
    │   ├── index.php             ← Ponto de entrada principal
    │   ├── .htaccess             ← Configurações de roteamento
    │   └── assets/               ← Arquivos estáticos (CSS, JS, imagens)
    │       ├── css/
    │       │   └── style.css
    │       └── js/
    │           └── app.js
    ├── App/                      ← Código da aplicação
    ├── views/                    ← Templates PHP
    ├── config/                   ← Configurações
    └── routes/                   ← Definições de rotas
```

### 4. Verificações Importantes

**Permissões dos arquivos:**
```bash
# Definir proprietário correto (ajuste conforme seu servidor)
sudo chown -R www-data:www-data /var/www/vhosts/arbit.duckdns.org/httpdocs/

# Definir permissões corretas
sudo chmod -R 755 /var/www/vhosts/arbit.duckdns.org/httpdocs/
sudo chmod -R 644 /var/www/vhosts/arbit.duckdns.org/httpdocs/backend/public/index.php
```

**Testar se o PHP está funcionando:**
```bash
# Criar um arquivo de teste
echo "<?php phpinfo(); ?>" > /var/www/vhosts/arbit.duckdns.org/httpdocs/backend/public/test.php

# Acessar: https://arbit.duckdns.org/test.php
# Depois remover: rm /var/www/vhosts/arbit.duckdns.org/httpdocs/backend/public/test.php
```

### 5. Configuração SSL (Recomendado)

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-apache

# Obter certificado SSL
sudo certbot --apache -d arbit.duckdns.org

# Renovação automática
sudo crontab -e
# Adicionar: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 6. Teste Final

Após aplicar as configurações:

1. **Acesse**: `https://arbit.duckdns.org/`
2. **Deve redirecionar para**: `/login` (se não logado) ou `/dashboard` (se logado)
3. **Teste login**: Use `admin@admin.com` / `password`
4. **Verifique assets**: CSS e JS devem carregar corretamente

## 🔧 Troubleshooting

### Erro 500 - Internal Server Error
- Verifique permissões dos arquivos
- Verifique logs: `sudo tail -f /var/log/apache2/error.log`
- Certifique-se de que `mod_rewrite` está habilitado

### Erro 404 - Not Found
- Verifique se o `DocumentRoot` aponta para `backend/public/`
- Verifique se o arquivo `index.php` existe
- Teste a configuração: `sudo apache2ctl configtest`

### CSS/JS não carregam
- Verifique se os arquivos existem em `backend/public/assets/`
- Verifique permissões dos arquivos estáticos
- Teste acesso direto: `https://arbit.duckdns.org/assets/css/style.css`

### Problemas de Roteamento
- Verifique se o `.htaccess` está no diretório `public/`
- Certifique-se de que `AllowOverride All` está configurado
- Verifique se `mod_rewrite` está habilitado

## 📞 Suporte

Se precisar de ajuda adicional:
1. Verifique os logs de erro: `sudo tail -f /var/log/apache2/error.log`
2. Teste a configuração: `sudo apache2ctl configtest`
3. Confirme que todos os módulos necessários estão habilitados
4. Verifique permissões de arquivos e diretórios