# Database Setup - CryptoArb Pro

Este diretório contém os scripts SQL necessários para configurar o banco de dados da aplicação CryptoArb Pro.

## 📁 Arquivos Disponíveis

### `setup.sql` (RECOMENDADO)
Script completo que inclui:
- ✅ Criação de todas as tabelas
- ✅ Inserção de dados iniciais (usuários, planos, configurações)
- ✅ Dados de exemplo para teste
- ✅ Verificação dos dados inseridos

### `quick-setup.sql`
Script básico que inclui apenas:
- ✅ Criação de todas as tabelas
- ❌ Sem dados iniciais

## 🚀 Como Usar

### Opção 1: phpMyAdmin (Recomendado)
1. Acesse o phpMyAdmin do seu provedor de hospedagem
2. Selecione seu banco de dados
3. Vá na aba "SQL"
4. Cole o conteúdo do arquivo `setup.sql`
5. Clique em "Executar"

### Opção 2: Linha de Comando
```bash
mysql -u seu_usuario -p seu_banco_de_dados < setup.sql
```

### Opção 3: Ferramenta de Gerenciamento
- MySQL Workbench
- DBeaver
- HeidiSQL
- Qualquer cliente MySQL

## 👥 Usuários Criados

Após executar o `setup.sql`, você terá os seguintes usuários:

### Administrador
- **Email**: `admin@admin.com`
- **Senha**: `password`
- **Saldo Principal**: $50,000.00
- **Saldo Bot**: $25,000.00

### Usuário Teste
- **Email**: `user@user.com`
- **Senha**: `password`
- **Saldo Principal**: $10,000.00
- **Saldo Bot**: $5,000.00

### Outros Usuários de Teste
- **João Silva**: `joao@email.com` - Senha: `password`
- **Maria Santos**: `maria@email.com` - Senha: `password`
- **Pedro Costa**: `pedro@email.com` - Senha: `password` (Suspenso)

## 📊 Dados Incluídos

- **3 Planos de Investimento** (Iniciante, Intermediário, Avançado)
- **7 Configurações do Sistema** (arbitragem, bot, taxas, etc.)
- **Operações de Arbitragem** de exemplo
- **Configurações de Bot** para usuários
- **Investimentos Ativos** de exemplo

## 🔧 Configuração Necessária

Após executar o script SQL, certifique-se de:

1. **Configurar o arquivo `.env`** com as credenciais do banco
2. **Testar a conexão** acessando a aplicação
3. **Fazer login** com as credenciais fornecidas

## ⚠️ Importante

- **Senha padrão**: Todos os usuários têm a senha `password`
- **Hash da senha**: `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`
- **Altere as senhas** em produção por segurança
- **Backup**: Sempre faça backup antes de executar scripts SQL

## 🆘 Problemas Comuns

### "Table already exists"
- Use `DROP TABLE IF EXISTS nome_tabela;` antes de criar
- Ou use `CREATE TABLE IF NOT EXISTS` (já incluído nos scripts)

### "Foreign key constraint fails"
- Certifique-se de que as tabelas são criadas na ordem correta
- O script já está na ordem correta

### "Access denied"
- Verifique as permissões do usuário MySQL
- Certifique-se de que o usuário tem privilégios CREATE e INSERT

## 📞 Suporte

Se encontrar problemas:
1. Verifique os logs de erro do MySQL
2. Confirme as credenciais de acesso ao banco
3. Teste a conexão com uma ferramenta de cliente MySQL