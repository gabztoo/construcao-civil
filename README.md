# Hermes - Sistema de Gestão para Construção Civil

Sistema desenvolvido em PHP 8.2+ com arquitetura MVC simples, Tailwind CSS via CDN e Alpine.js para interatividade.

## Requisitos

- PHP 8.2+
- MySQL 8.0+ / MariaDB 10.6+
- Extensões PHP: `pdo_mysql`, `mbstring`, `gd`, `bcmath`
- Composer (para autoload e dependências)
- Servidor web (Apache/Nginx) ou PHP built-in server

## Instalação Rápida

### 1. Clone e configure

```bash
git clone <repo-url> hermes
cd hermes
```

### 2. Instale dependências

```bash
composer install
```

### 3. Configure o ambiente

```bash
cp .env.example .env
# Edite .env com suas credenciais do banco
```

### 4. Crie o banco de dados

```sql
CREATE DATABASE hermes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Execute as migrations

```bash
php database/migrate.php
```

Isso criará as tabelas e o usuário admin:
- **Email:** admin@hermes.local
- **Senha:** admin123

### 6. Configure o servidor web

**Apache (XAMPP/Hostinger):**
- Document Root: `public/`
- O `.htaccess` já redireciona tudo para `public/index.php`

**PHP Built-in Server (desenvolvimento):**
```bash
php -S localhost:8000 -t public
```

Acesse: http://localhost:8000 (ou sua URL configurada)

## Estrutura do Projeto

```
hermes/
├── public/                 # Document Root
│   ├── index.php          # Front Controller
│   ├── assets/css/        # CSS customizado
│   └── uploads/           # Uploads de arquivos
├── src/
│   ├── Config/            # Database, App config
│   ├── Core/              # Classes base (Controller, Model, Auth, etc)
│   ├── Modules/           # Módulos de domínio
│   │   ├── Auth/          # Autenticação
│   │   └── Obras/         # Gestão de Obras
│   └── Views/             # Templates PHP
├── database/
│   ├── migrations/        # SQL migrations
│   └── migrate.php        # Runner de migrations
├── .env                   # Configurações (não versionar)
├── .env.example
├── composer.json
└── .htaccess
```

## Funcionalidades MVP (Semana 1)

- ✅ **Autenticação**: Login/Logout com sessão segura, CSRF, Argon2id
- ✅ **RBAC**: 5 papéis (admin, engenheiro, mestre, comprador, financeiro)
- ✅ **Dashboard**: KPIs de obras (total, andamento, concluídas, atrasadas, orçado)
- ✅ **CRUD Obras**: Listagem com busca/filtros/paginação, criar, editar, ver, excluir
- ✅ **Etapas**: Visualização de progresso por obra
- ✅ **UI Responsiva**: Sidebar colapsável, dark mode, Tailwind CDN

## Deploy na Hostinger

1. **No hPanel:**
   - Crie banco MySQL
   - Configure **Git Deploy** → repositório → branch `main` → pasta `public_html`
   - Defina **Document Root** como `public_html/public`
   - PHP 8.2 + extensões necessárias

2. **Variáveis de ambiente (.env na raiz):**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://seudominio.com
   DB_HOST=seu-host-mysql.hostinger.com
   DB_NAME=seu_banco
   DB_USER=seu_user
   DB_PASS=sua_senha_forte
   ```

3. **Deploy:**
   ```bash
   git add .
   git commit -m "Deploy"
   git push origin main
   ```

4. **Execute migrations no servidor:**
   - Via terminal SSH: `php database/migrate.php`
   - Ou crie as tabelas manualmente no phpMyAdmin usando os arquivos em `database/migrations/`

## Comandos Úteis

```bash
# Rodar migrations
php database/migrate.php

# Limpar cache (se implementado)
php artisan cache:clear  # N/A - não usa Laravel

# Testar conexão DB
php -r "require 'vendor/autoload.php'; \$d = Dotenv\Dotenv::createImmutable('.'); \$d->load(); require 'src/Config/Database.php'; var_dump(Database::getInstance());"
```

## Próximos Passos (Roadmap)

- [ ] Módulo Suprimentos/Almoxarifado
- [ ] Módulo Compras/Cotações
- [ ] Módulo Financeiro (CP/CR/Medições)
- [ ] Módulo Pessoas/Ponto
- [ ] Relatórios PDF/Excel
- [ ] API para app mobile
- [ ] Testes automatizados (Pest)

## Licença

Proprietário - Hermes Gestão de Obras