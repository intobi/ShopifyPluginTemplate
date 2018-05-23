Requirements:
-
1)

Shopify Installation
-
1)

App Installation:
-


1) git clone 
2) cp .env.example .env
3) define variables in .env
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD
- SHOPIFY_API_KEY
- SHOPIFY_API_SECRET
4) composer install && composer dump-autoload
5) php artisan key:generate
6)  php artisan migrate

Start
-