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

### Run tests
- ./vendor/bin/sail artisan test

### Run static analysis (Larastan)
- ./vendor/bin/sail php ./vendor/bin/phpstan analyse

### Run code style checker
- ./vendor/bin/sail php ./vendor/bin/pint
