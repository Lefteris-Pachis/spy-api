# spy-api
 A RESTful API with Laravel to manage a system of famous spies.

## Project setup

Please run the following commands in order to setup the project
```sh
cd backend/
cp .env.example .env
```
Then continue running the following commands:
```sh
docker-compose build
docker-compose up -d
docker-compose exec laravel sh
composer install
php artisan key:generate
php artisan optimize:clear
php artisan migrate
php artisan db:seed
exit
```

