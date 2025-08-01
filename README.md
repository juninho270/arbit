# CryptoArb Pro - Plataforma de Arbitragem de Criptomoedas

Uma plataforma completa para arbitragem de criptomoedas com frontend React.

## 🚀 Tecnologias

### Frontend
- **React 18** com TypeScript
- **Tailwind CSS** para estilização
- **React Router** para navegação
- **Recharts** para gráficos
- **Axios** para requisições HTTP
- **Lucide React** para ícones

## 📁 Estrutura do Projeto

```
cryptoarb-pro/
├── src/                     # Frontend React
│   ├── components/
│   ├── contexts/
│   ├── utils/
│   └── ...
└── ...
```

## 🛠️ Instalação e Configuração

### Frontend (React)

```bash
# Instalar dependências
npm install

# Configurar ambiente
cp .env.example .env

# Configurar URL da API no .env
VITE_API_URL=http://localhost:3001/api

# Iniciar servidor de desenvolvimento
npm run dev
```

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

- **Autenticação JWT** 
- **Middleware de autorização** para rotas protegidas
- **Validação de dados** no frontend e backend
- **Proteção CORS** configurada
- **Sanitização de inputs** para prevenir XSS

## 🚀 Deploy

### Frontend (React)
1. Execute `npm run build`
2. Faça upload dos arquivos da pasta `dist/`
3. Configure servidor web para SPA
4. Configure variáveis de ambiente de produção

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