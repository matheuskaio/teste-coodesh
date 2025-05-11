# 🥗 Backend Challenge 20230105 - OpenFood Sync API

API RESTful construída em Laravel 12 para importar, listar e gerenciar produtos alimentícios do projeto [Open Food Facts](https://br.openfoodfacts.org/).

> This is a challenge by [Coodesh](https://coodesh.com/)

---

## 📋 Descrição

A API tem como objetivo fornecer suporte à equipe de nutricionistas da empresa Fitness Foods LC, permitindo que acessem e validem dados nutricionais dos produtos alimentícios cadastrados pelos usuários no aplicativo.

---

## 🚀 Tecnologias utilizadas

-   PHP 8.2 + Laravel 12
-   MySQL (via Docker)
-   Composer
-   Docker + Docker Compose
-   PHPUnit
-   Swagger (OpenAPI 3.0)
-   Mailhog (simulação de e-mails)
-   Laravel Scheduler (cron jobs)

---

## 🔧 Instalação e uso

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/openfood-sync.git
cd openfood-sync
```

### 2. Copie o `.env` e configure

```bash
cp .env.example .env
```

### 3. Suba o ambiente com Docker

```bash
docker-compose up -d --build
```

### 4. Instale as dependências no container

```bash
docker exec -it laravel-app composer install
docker exec -it laravel-app php artisan migrate
```

---

## 📅 Agendamento automático

O sistema executa um cron todos os dias às **3h da manhã**, que:

-   Acessa os arquivos delta do Open Food Facts
-   Importa até 100 produtos por arquivo
-   Salva o histórico de importação

Você pode testar manualmente com:

```bash
docker exec -it laravel-scheduler php artisan schedule:run
```

---

## 🔐 Autenticação por API Key

Adicione o header abaixo em todas as requisições:

```http
X-API-KEY: sua_chave_configurada
```

---

## 🧪 Testes

Execute os testes com:

```bash
docker exec -it laravel-app php artisan test
```

---

## 🧾 Documentação da API

A documentação está disponível no formato [OpenAPI 3.0 (swagger.json)](./swagger.json)

Você pode visualizar em:

-   https://editor.swagger.io
-   Postman > Import > raw file

---

## 📂 Endpoints principais

-   `GET /` — Status do sistema
-   `GET /products` — Lista paginada de produtos
-   `GET /products/{code}` — Detalhes de um produto
-   `PUT /products/{code}` — Atualizar produto
-   `DELETE /products/{code}` — Marcar como `trash`

---

## 📧 Notificações de erro

Falhas no sync enviam e-mail para o administrador configurado.

---

## 📝 Histórico de importações

Verifique a última execução da importação via endpoint `/`, campo `last_cron_run`.

---

---

## ⚙️ Arquivo .env

Renomeie o arquivo `.env.example` para `.env`:

```bash
cp .env.example .env
```

### ✉️ Configuração do MailHog

```
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_FROM_ADDRESS=noreply@openfoodapi.com
MAIL_FROM_NAME="OpenFood Sync"
```

### 🛢️ Configuração do MySQL

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root
```

### 🔐 Chave de autenticação

```
API_KEY=supersecreta123
```

---

## 🧭 Passo a Passo

### 1. Subir os Containers

Navegue até a pasta raiz do projeto (onde está o arquivo `docker-compose.yml`) e execute:

```bash
docker compose up -d --build
```

_Aguarde até que todos os serviços estejam rodando_

---

### 2. Configurar o Projeto Laravel

Após os containers estarem ativos, acesse o container principal:

```bash
docker exec -it app bash
```

Dentro do container, execute os seguintes comandos:

#### 2.1. Ajustar permissões

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

#### 2.2. Executar migrações do banco de dados

```bash
php artisan migrate
```

#### 2.3. Gerar o valor da APP_KEY

```bash
php artisan key:generate
```

#### 2.4. Executar a importação dos produtos manualmente (opcional)

```bash
php artisan openfood:import
```

#### 2.5. Sair do container

```bash
exit
```

---

### 3. Testar a Aplicação

Acesse o endpoint de status para verificar se a API está funcionando:

```
GET http://localhost:8080/api/status
```

Resposta esperada:

```json
{
    "status": "OK",
    "database_connection": "OK",
    "last_cron_run": "Nunca executado",
    "uptime": "0 seconds",
    "memory_usage_mb": 20
}
```
