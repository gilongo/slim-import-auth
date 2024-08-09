# PHP Slim - Import & Auth

Simple PHP Slim Application that imports a list of users via csv file and serves some routes for authentication.

## Requirements

To run this application you'll need Docker installed on your machine.
<br>This application was developed using Docker engine v25.0.3.

## Get Started

To start using this application, you have to run the `docker-compose up --build -d` command. This creates the enviroment where the backend application and the database is hosted. <br>
The entrypoint is `localhost:8080`.

## Application Cookbook

- Run migrations: `docker exec slim_app vendor/bin/phinx migrate`
- Run Users import script: `docker exec slim_app ./import users.csv`
