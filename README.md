# Spy API
 A RESTful API with Laravel to manage a system of famous spies.

## Project setup

Please run the following commands in order to setup the project
1. Clone the Repository:
```sh
git clone <repository-url>
cd spy-api/backend
cp .env.example .env
```
2. Build and Start the Docker Containers:
```sh
docker-compose build
docker-compose up -d
```
3. Install Dependencies and Generate Key:
```sh
docker-compose exec laravel sh
composer install
php artisan key:generate
php artisan optimize:clear
```
4. Migrate and Seed the Database:
```sh
php artisan migrate
php artisan db:seed
```
5. Run the Tests (optional):
```sh
php artisan test
```
6. Access the API: The API will be available at http://localhost:8085/api/spies

You can import the postman collection included in the file: [spies-api.postman_collection.json](spies-api.postman_collection.json)

## Command Documentation

#### Create a Spy

To create a new spy record, use the following command:
```sh
php artisan spy:create "James" "Bond" "MI6" "United Kingdom" "1920-04-13"
```

## Future Improvements
- Caching queries to reduce database load.
- Implementing job queues for sending notifications when spies are created.
- Improvements to the tests.
- Expanding current relationships between spies and agencies.
- Implement a robust authentication system (e.g., JWT or OAuth) to secure the API endpoints and manage user roles and permissions.