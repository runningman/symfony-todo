# Symfony 4 Todo app

Demo todo application built in Symfony 4.

## Setup

- Install the composer dependencies `composer install`
- Create a `.env.local` file with your database settings (DATABASE_URL)
- Run the migrations: `php bin/console doctrine:migrations:migrate`
- Loading fixtures (optional): `php bin/console hautelook:fixtures:load`
- To run the tests: `bin/phpunit`

## Online demo

https://todo.runningman.be