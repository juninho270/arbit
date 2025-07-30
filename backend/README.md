# CryptoArb Pro - Backend API

Backend em PHP Laravel com MySQL para a plataforma de arbitragem de criptomoedas.

## Requisitos

- PHP 8.1+
- Composer
- MySQL 5.7+
- Laravel 10

## Instalação

1. **Instalar dependências:**
```bash
cd backend
composer install
```

2. **Configurar ambiente:**
```bash
cp .env.example .env
```

3. **Configurar banco de dados no `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cryptoarb_pro
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

4. **Gerar chave da aplicação:**
```bash
php artisan key:generate
```

5. **Executar migrações e seeders:**
```bash
php artisan migrate --seed
```

6. **Iniciar servidor:**
```bash
php artisan serve
```

A API estará disponível em `http://localhost:8000`

## Estrutura da API

### Autenticação
- `POST /api/login` - Login
- `POST /api/register` - Registro
- `POST /api/logout` - Logout
- `GET /api/me` - Usuário atual
- `POST /api/login-as-user` - Admin login como usuário

### Usuários
- `GET /api/users` - Listar usuários (Admin)
- `POST /api/users` - Criar usuário (Admin)
- `GET /api/users/{id}` - Ver usuário
- `PATCH /api/users/{id}` - Atualizar usuário
- `DELETE /api/users/{id}` - Excluir usuário (Admin)
- `PATCH /api/users/{id}/balance` - Atualizar saldo

### Criptomoedas
- `GET /api/cryptocurrencies` - Listar criptomoedas
- `GET /api/cryptocurrencies/{coinId}/price` - Preço da criptomoeda
- `PATCH /api/cryptocurrencies/{coinId}/arbitrage-status` - Status arbitragem (Admin)

### Arbitragem
- `GET /api/arbitrage/operations` - Listar operações
- `POST /api/arbitrage/execute-manual` - Executar arbitragem manual
- `GET /api/arbitrage/recent` - Operações recentes

### Bot
- `GET /api/bot/settings` - Configurações do bot
- `PATCH /api/bot/settings` - Atualizar configurações
- `GET /api/bot/statistics` - Estatísticas do bot

### Investimentos
- `GET /api/investments/plans` - Planos de investimento
- `GET /api/investments/user` - Investimentos do usuário
- `POST /api/investments` - Criar investimento
- `GET /api/investments/statistics` - Estatísticas

### Admin
- `GET /api/admin/stats` - Estatísticas do sistema
- `GET /api/admin/system-settings` - Configurações do sistema
- `PATCH /api/admin/system-settings` - Atualizar configurações
- `GET /api/admin/recent-activity` - Atividade recente

## Usuários Padrão

Após executar os seeders, você terá:

**Admin:**
- Email: `admin@admin.com`
- Senha: `password`

**Usuário Teste:**
- Email: `user@user.com`
- Senha: `password`

## Configurações Importantes

### CORS
Configure as URLs do frontend no arquivo `config/cors.php` ou na variável `FRONTEND_URL` do `.env`.

### APIs Externas
- CoinGecko API para preços de criptomoedas
- Moralis API para transações blockchain (configure a chave no `.env`)

### Autenticação
Utiliza Laravel Sanctum para autenticação via tokens API.

## Deploy

Para deploy em produção:

1. Configure o servidor web (Apache/Nginx)
2. Configure o banco de dados MySQL
3. Execute `composer install --optimize-autoloader --no-dev`
4. Configure as variáveis de ambiente
5. Execute `php artisan migrate --seed`
6. Configure as permissões de pasta
7. Configure SSL/HTTPS

## Estrutura do Banco

- `users` - Usuários do sistema
- `cryptocurrencies` - Dados das criptomoedas
- `arbitrage_operations` - Operações de arbitragem
- `bot_settings` - Configurações dos bots
- `investment_plans` - Planos de investimento
- `investments` - Investimentos dos usuários
- `system_settings` - Configurações do sistema