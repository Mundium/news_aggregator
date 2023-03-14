# Innoscripta News aggregator Backend V1

## Installation

1. Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/5.4/installation#installation)

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Generate a new secret key (JWT)

    php artisan jwt:secret

Configure database connection in .env file

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=database_name
    DB_USERNAME=database_username
    DB_PASSWORD=database_password

Start the local development server

    php artisan serve

You can now test the api from http://localhost:8000/api/...

---

##Run Application with Docker

### Pre-requisites

- Docker running on the host machine.
- Docker compose running on the host machine.
- Basic knowledge of Docker.

### Run

Clone the repo

[//]: # (    git clone git@ssh.dev.azure.com:v3/avaliance/MyPrisme/myPrisme-backend-v2)

Change directory

    cd backend

Build and run the Docker containers

    docker compose up -d

This builds the containers and runs them in the background, while this

    docker compose up

builds the containers to outputs their logs to the console.

Install Application

    docker compose exec news_aggregator_app bash

- !!! if it is your first time running the command please note that you have to repeat the steps in the Installation section

- instead of: 

Configure database connection in .env file

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=database_name
    DB_USERNAME=database_username
    DB_PASSWORD=database_password


- Do: 

Configure database connection in .env file

    DB_CONNECTION=mysql
    DB_HOST=news_aggregator_inno_db
    DB_PORT=52000
    DB_DATABASE=news_aggregator
    DB_USERNAME=root
    DB_PASSWORD=root


- run this command if you face this error "The stream or file "/myfolder/instantpay/storage/logs/laravel.log" could not be opened in append mode: failed to open stream: Permission denied"


    chmod 777 -R storage bootstrap/cache

- You should be able to visit your app at http://localhost:8000

- To visit documentation http://localhost:8000/api/documentation

To stop the containers run `docker-compose kill`, and to remove them run `docker-compose rm`

---

## Folders

- `app` - Contains all the Eloquent models
- `app/Http/Controllers` - Contains all the laravel auth controllers
- `app/Api/Back/V1/Controllers/` - Contains all the laravel API BACKEND controllers
- `app/Api/Front/V1/Controllers/` - Contains all the laravel API FRONTEND controllers
- `app/Http/Middleware` - Contains the JWT auth middleware
- `config` - Contains all the application configuration files
- `database/migrations` - Contains all the database migrations for the APP
- `routes` - Contains all the routes files
- `routes/api.php`  - Contains all the API routes

## Troubleshooting

### Problem:

I get **Could not get any response** in postman when sending requests to the server

### Solution:

Go to Postman settings -> Proxy, Enable **Global Proxy Configuration** and check HTTP & HTTPS then configure the proxy input with localhost:8000 and Try again.

---
