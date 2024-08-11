# PHP Slim - Import & Auth

Simple PHP Slim Application that imports a list of users via csv file and serves some routes for authentication.

## Requirements

To run this application you'll need Docker installed on your machine.
<br>This application was developed using Docker engine v25.0.3.

## Get Started

To start using this application, you have to run the `docker-compose up --build -d` command. This creates the enviroment where the backend application and the database is hosted. <br>
After that you'll have to install the composer dependencies, running `composer install`. <br>
Automatically, the `post-dump-autoload` script will run migrations for users table and import the users running the `import` script. <br>
Now the application should be up and running at `localhost:8080`.

## Application Cookbook

- Run migrations: `docker exec slim_app vendor/bin/phinx migrate`
- Run Users import script: `docker exec slim_app ./import data/users.csv`
