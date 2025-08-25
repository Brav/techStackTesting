# Gambling Assigment Test

## Installation Guide

### Install PHP dependencies
- composer install

### Create env from the example env
- cp .env.example .env
- php artisan key:generate

### Start Sail
- ./vendor/bin/sail up -d

### Migrate DB and seed test data
- ./vendor/bin/sail artisan migrate --seed

### Install frontend dependencies
- ./vendor/bin/sail npm install

### Run frontend watcher
- ./vendor/bin/sail npm run dev

### Access URL
- Use following url for accessing app: http://localhost:8080/#/

### Run tests
- ./vendor/bin/sail artisan test

### Run static analysis (Larastan)
- ./vendor/bin/sail php ./vendor/bin/phpstan analyse

### Run code style checker
- ./vendor/bin/sail php ./vendor/bin/pint
