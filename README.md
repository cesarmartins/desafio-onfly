# desafio-onfly
Desafio para vaga de Desenvolvedor PHP na Onfly - https://www.onfly.com.br/

# Instalação
docker-compose up -d --build

# migrate
docker exec -it travel-app php artisan migrate
- se é a primeira vez ele vai perguntar se quer o database laravel - yes

# Confere os containers
docker ps

# Acesse o app container e roda as magias
docker exec -it travel-app bash
composer install
php artisan key:generate
php artisan migrate - Se rodou o migrate antes, não precisa rodar novamente, mas se quiser ele vai dizer que não tem nada para migrar, vai na sua!
php artisan config:clear

