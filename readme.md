# Getting started

To run this project you will need:
- PHP 8.5
- Symfony
- Docker (For the database)

After cloning the project run ``composer install`` in the project directory. To create the database run ``docker-composer up -d``. This should create a docker container which contains the database. If the database was not already created, run ``php bin/console doctrine:database:create``. To create the tables necessary for this app, run ``php bin/console doctrine:migrations:migrate``. Finally run ``symfony server:start`` to start the server.

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