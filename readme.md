# Getting started

To run this project you will need:
- PHP 8.5
- Symfony
- Docker (For the database)

After cloning the project run ``composer install`` in the project directory. To create the database run ``docker-composer up -d``. This should create a docker container which contains the database. If the database was not already created, run  
``php bin/console doctrine:database:create``.