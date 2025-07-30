# CryptoArb Pro - Plataforma de Arbitragem de Criptomoedas

Uma plataforma completa para arbitragem de criptomoedas com frontend React e backend Laravel.

## 🚀 Tecnologias

### Frontend
- **React 18** com TypeScript
- **Tailwind CSS** para estilização
- **React Router** para navegação
- **Recharts** para gráficos
- **Axios** para requisições HTTP
- **Lucide React** para ícones

### Backend
- **PHP 8.1+** com Laravel 10
- **MySQL** para banco de dados
- **Laravel Sanctum** para autenticação
- **APIs externas**: CoinGecko, Moralis

## 📁 Estrutura do Projeto

```
cryptoarb-pro/
├── backend/                 # API Laravel
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   └── ...
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── ...
├── src/                     # Frontend React
│   ├── components/
│   ├── contexts/
│   ├── utils/
│   └── ...
└── ...
```

## 🛠️ Instalação e Configuração

### 1. Backend (Laravel)

```bash
# Navegar para o diretório backend
cd backend

# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate

# Configurar banco de dados no .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cryptoarb_pro
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# Executar migrações e seeders
php artisan migrate --seed

# Iniciar servidor
php artisan serve
```

### 2. Frontend (React)

```bash
# Instalar dependências
npm install

# Configurar ambiente
cp .env.example .env

# Configurar URL da API no .env
VITE_API_URL=http://localhost:8000/api

# Iniciar servidor de desenvolvimento
npm run dev
```

## 👥 Usuários Padrão

Após executar os seeders, você terá acesso aos seguintes usuários:

### Administrador
- **Email**: `admin@admin.com`
- **Senha**: `password`
- **Acesso**: Painel administrativo completo

### Usuário Teste
- **Email**: `user@user.com`
- **Senha**: `password`
- **Acesso**: Funcionalidades de usuário

## 🔧 Funcionalidades

### Para Usuários
- ✅ **Dashboard** com estatísticas e gráficos
- ✅ **Arbitragem Manual** com execução em tempo real
- ✅ **Bot de Trading** configurável
- ✅ **Mercado de Criptomoedas** com preços atualizados
- ✅ **Planos de Investimento** com diferentes níveis de risco
- ✅ **Configurações de Conta** personalizáveis

### Para Administradores
- ✅ **Painel Administrativo** completo
- ✅ **Gerenciamento de Usuários** (criar, editar, excluir)
- ✅ **Controle de Saldos** (principal e bot)
- ✅ **Login como Usuário** com um clique
- ✅ **Gerenciamento de Criptomoedas** (ativar/desativar arbitragem)
- ✅ **Configurações do Sistema** globais
- ✅ **Estatísticas e Relatórios** em tempo real

## 🔐 Segurança

- **Autenticação JWT** via Laravel Sanctum
- **Middleware de autorização** para rotas protegidas
- **Validação de dados** no frontend e backend
- **Proteção CORS** configurada
- **Sanitização de inputs** para prevenir XSS

## 📊 APIs Integradas

### CoinGecko API
- Preços de criptomoedas em tempo real
- Dados de mercado e capitalização
- Histórico de preços

### Moralis API
- Transações blockchain
- Dados de contratos inteligentes
- Verificação de transações reais

## 🚀 Deploy

### Backend (Laravel)
1. Configure servidor web (Apache/Nginx)
2. Configure banco de dados MySQL
3. Execute `composer install --optimize-autoloader --no-dev`
4. Configure variáveis de ambiente
5. Execute `php artisan migrate --seed`
6. Configure permissões de pasta
7. Configure SSL/HTTPS

### Frontend (React)
1. Execute `npm run build`
2. Faça upload dos arquivos da pasta `dist/`
3. Configure servidor web para SPA
4. Configure variáveis de ambiente de produção

## 📝 Endpoints da API

### Autenticação
- `POST /api/login` - Login
- `POST /api/register` - Registro
- `POST /api/logout` - Logout
- `GET /api/me` - Usuário atual

### Usuários
- `GET /api/users` - Listar usuários
- `POST /api/users` - Criar usuário
- `PATCH /api/users/{id}` - Atualizar usuário
- `DELETE /api/users/{id}` - Excluir usuário

### Arbitragem
- `GET /api/arbitrage/operations` - Listar operações
- `POST /api/arbitrage/execute-manual` - Executar arbitragem
- `GET /api/arbitrage/recent` - Operações recentes

### Admin
- `GET /api/admin/stats` - Estatísticas do sistema
- `GET /api/admin/system-settings` - Configurações
- `PATCH /api/admin/system-settings` - Atualizar configurações

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

Para suporte e dúvidas:
- Abra uma issue no GitHub
- Entre em contato via email

---

**CryptoArb Pro** - Desenvolvido com ❤️ para traders de criptomoedas