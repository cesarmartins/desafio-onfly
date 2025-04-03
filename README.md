# ✈️ Microserviço de Viagens Corporativas – Laravel

API REST para gerenciamento de pedidos de viagem corporativa.  
Construída com Laravel, protegida com Sanctum, documentada com Swagger, e amada por você.

---

## 📦 Tecnologias

- Laravel 12
- Sanctum (auth via token)
- MySQL (via Docker)
- Swagger (l5-swagger)
- PHPUnit (testes automatizados)
- Docker / Docker Compose

---
## 🚀 Como rodar o projeto

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/desafio-onfly.git
cd desafio-onfly
```
### 2. Suba com Docker 🐳
```bash
docker-compose up -d
```

---
# ⚙️ Configuração do ambiente

### 1. Copie o .env.example:
```bash
cp .env.example .env
```

### 2. Gere a chave de aplicação:
```bash
cp .env.example .env
```

### 3. Ajuste o .env para usar MySQL:
```bash
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=travel_db
DB_USERNAME=travel_user
DB_PASSWORD=secret
```

### 4. Rode as migrations:
```bash
php artisan migrate
```
---
# 🔐 Autenticação

A API usa Laravel Sanctum. Para gerar um token:

### Endpoint
```bash
php artisan migrate
```
### Body
```bash
{
  "email": "cesar@onfly.dev",
  "password": "password"
}   
```
### Se não existir um usuário, crie via Tinker:
```bash
php artisan tinker
\App\Models\User::factory()->create([
  'email' => 'cesar@onfly.dev',
  'password' => bcrypt('password')
]);
```
### Token de retorno:
```bach
{
  "token": "1|laravel-token-aqui"
}
```
### Use o token nas rotas protegidas:
```bash
Authorization: Bearer SEU_TOKEN_AQUI
```
---
# 📖 Documentação da API (Swagger)

Gerada automaticamente com l5-swagger.

```bach
http://localhost:8000/api/documentation
```
Use o botão "Authorize" para colar seu token JWT do login.

---
# 🔍 Endpoints principais

| Método | Endpoint                      | Descrição                          |
GET	/api/travel-requests	Listar pedidos (com filtros)
GET	/api/travel-requests/{id}	Ver detalhes de um pedido
POST	/api/travel-requests	Criar novo pedido
PATCH	/api/travel-requests/{id}/status	Atualizar status (aprovado/cancelado)
POST	/api/login	Gera token de autenticação  

    
# 🧪 Testes automatizados
```bash
php artisan test
```
Ou, se quiser filtrar:
```bash
php artisan test --filter=TravelRequestTest
```
---
# ✨ Organização do Projeto
- **TravelRequestController**: com Resource, Service, FormRequest.
- **AuthController**: para login e geração de token.
- Filtros via query string (status, destino, datas)
- Lógica de negócio: só pedidos aprovados podem ser cancelados
- Logs de notificação (fake) 
- Swagger documentado com @OA (OpenAPI)

---
# ✅ Considerações Técnicas
- Sanctum foi escolhido por oferecer uma autenticação baseada em token simples, segura e bem integrada ao Laravel, sem precisar reinventar a roda.
JWT puro (ex: tymon/jwt-auth) pode ser integrado em uma versão futura, caso o microserviço passe a funcionar em um ambiente distribuído real.

---
# 💡 Autor
- Feito por César Martins
- 📮 cesar.martins01@gmail.com
