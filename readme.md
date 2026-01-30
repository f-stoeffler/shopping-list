# Getting started

To run this project you will need:
- PHP 8.5
- Symfony
- Composer
- Docker (For the database)

After cloning the project run ``composer install`` in the project directory. To create the database run ``docker-compose -f .\dev-docker-compose.yml up -d``. This should create a docker container which contains the database (If you have previously docker-composed the database with different credentials, you may need to clean your docker images and/or volumes). If the database was not created with docker-compose, run ``php bin/console doctrine:database:create``. To create the tables necessary for this app, run ``php bin/console doctrine:migrations:migrate``. Finally, run ``symfony server:start`` to start the server.

These are the API endpoints available together with examples of the data structure for POST/PUT and responses for GET:
- POST ``/api/lists`` - Create a shopping list with or without items, responds with complete shopping list, responds with the new list
  - ``{"name": "Posted List", "items": [{"name": "Included Item", acquired: true}]}``
- POST ``/api/lists/{id}/item`` - Create an item in the shopping list that has the ID {id}, responds with complete shopping list
  - ``{"name": "Posted Item", "acquired": true}``
- GET ``/api/lists/{id}/items`` - Responds with complete shopping list
  - ``{"id": 1, "name": "Gotten List", "items": [{"id": 1, "name": "Item1", acquired: true}, {"id": 2, "name": "Item2", acquired: false}]}``
- GET ``/api/lists/{id}/items/{itemId}`` - Responds with the item data and the shopping list it corresponds to
  - ``{"acquired": true, "id": 1, "name": "Gotten Item", "shopping_list": { "id": 1, "name": "Corresponding List"}}``
- PUT ``/api/lists/{id}/items/{itemId}`` - Updates an item, responds with the new item
  - ``{"name": "Put item", "acquired": true}``
- DELETE ``/api/lists/{id}/items/{itemId}`` - Removes an item, responds with the new shopping list
  - ``{"name": "List", "items": [{"name": "Not deleted item", acquired: true}, {"name": "Not deleted item 2", acquired: true}]}``
- DELETE ``/api/lists/{id}`` - Removes a shopping list, responds with 204 No Content

You can try out these API endpoints at ``https://shopping-list.florian-stoeffler.at/api/lists/``

# Deploying

Disclaimer: I have only tested this on Ubuntu Linux.

To deploy this webapp, rename ``.env.example`` to ``.env.prod`` and replace the placeholder values with secure ones. The app will run at port 9000 (0.0.0.0:9000), to change this, change all mentions of 9000 in ``docker-compose.yml``, ``Dockerfile`` and ``docker/nginx/default.conf`` to your desired port. Finally, run ``bash ./init.sh`` to deploy. Redirect your domain or subdomain to port 9000 or the port that you configured and your webapp should be reachable under https://www.example.com/lists