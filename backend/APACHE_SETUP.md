# Configuração do Apache para CryptoArb Pro

Este guia explica como configurar o Apache para servir corretamente a aplicação MVC pura.

## 🚨 Problema Atual

Os erros que você pode estar enfrentando:

### Erro 1: DirectoryIndex não encontrado
```
AH01276: Cannot serve directory /var/www/vhosts/arbit.duckdns.org/httpdocs/: No matching DirectoryIndex found
```

Indica que o Apache não consegue encontrar o arquivo `index.php` porque está procurando no diretório errado.

### Erro 2: DirectoryMatch não permitido
```
<DirectoryMatch not allowed here
```

Indica que algumas diretivas não são permitidas em arquivos `.htaccess`.

## ✅ Solução

### 1. Configurar o Virtual Host

Você precisa modificar a configuração do Virtual Host para apontar para o diretório `public` da aplicação.

**Localização do arquivo de configuração:**
- Ubuntu/Debian: `/etc/apache2/sites-available/arbit.duckdns.org.conf`
- CentOS/RHEL: `/etc/httpd/conf.d/arbit.duckdns.org.conf`
- Plesk: Através do painel de controle ou `/var/www/vhosts/system/arbit.duckdns.org/conf/`

**Use o arquivo `apache-vhost-example.conf` como base** e adapte conforme sua configuração.

### 2. Comandos para Aplicar

```bash
# 1. Copiar o arquivo de configuração (ajuste o caminho)
sudo cp apache-vhost-example.conf /etc/apache2/sites-available/arbit.duckdns.org.conf

# 2. Habilitar o site (Ubuntu/Debian)
sudo a2ensite arbit.duckdns.org.conf

# 3. Habilitar módulos necessários
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod ssl

# 4. Testar a configuração
sudo apache2ctl configtest

# 5. Reiniciar o Apache
sudo systemctl restart apache2
```

### 3. Verificar Permissões dos Arquivos .htaccess

```bash
# Verificar se os arquivos .htaccess existem e têm as permissões corretas
ls -la /var/www/vhosts/arbit.duckdns.org/httpdocs/backend/.htaccess
ls -la /var/www/vhosts/arbit.duckdns.org/httpdocs/backend/public/.htaccess

# Definir permissões corretas se necessário
chmod 644 /var/www/vhosts/arbit.duckdns.org/httpdocs/backend/.htaccess
chmod 644 /var/www/vhosts/arbit.duckdns.org/httpdocs/backend/public/.htaccess
```

### 3. Estrutura de Arquivos Esperada

Certifique-se de que sua estrutura de arquivos está assim:

```
/var/www/vhosts/arbit.duckdns.org/httpdocs/
├── backend/
│   ├── public/
│   │   ├── index.php          ← Ponto de entrada principal
│   │   └── .htaccess          ← Configurações de roteamento
│   ├── app/
│   ├── config/
│   ├── database/
│   └── .htaccess              ← Redirecionamento para public/
└── src/                       ← Frontend React (opcional)
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

### 5. Configuração SSL (Opcional)

Para resolver o aviso SSL, você pode obter um certificado gratuito:

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-apache

# Obter certificado SSL
sudo certbot --apache -d arbit.duckdns.org

# Renovação automática
sudo crontab -e
# Adicionar: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 6. Logs para Debugging

Se ainda houver problemas, verifique os logs:

```bash
# Logs de erro do Apache
sudo tail -f /var/log/apache2/error.log

# Logs específicos do site
sudo tail -f /var/log/apache2/arbit_error.log
```

### 7. Teste Final

Após aplicar as configurações:

1. **Acesse**: `https://arbit.duckdns.org/`
2. **Deve mostrar**: Resposta JSON da API ou página de login
3. **Teste API**: `https://arbit.duckdns.org/api/health`

## 🔧 Troubleshooting

### Erro 500 - Internal Server Error
- Verifique permissões dos arquivos
- Verifique logs de erro do Apache
- Certifique-se de que o módulo `mod_rewrite` está habilitado

### Erro 404 - Not Found
- Verifique se o `DocumentRoot` está correto
- Verifique se o arquivo `index.php` existe em `backend/public/`

### Problemas de CORS
- Ajuste as configurações de CORS no arquivo `.htaccess`
- Verifique se o frontend está acessando a URL correta da API

## 📞 Suporte

Se precisar de ajuda adicional:
1. Verifique os logs de erro
2. Teste a configuração do Apache: `sudo apache2ctl configtest`
3. Confirme que todos os módulos necessários estão habilitados