# Hackathon ESGI

## Install using docker-compose
* copy ``docker-compose.yml.dist`` to ``docker-compose.yml``

* copy ``.env.dist`` to ``.env`` and fill with the required parameters

* Run ``docker-compose up`` to build the network 

* ``docker-compose exec php-fpm composer install`` To install composer dependencies

* ``docker-compose exec nodejs bower install --allow-root`` To install bower ressources

* ``docker-compose exec nodejs yarn install`` To install yarn ressources

* ``docker-compose exec nodejs yarn run dev|build`` To compile the ressources for dev or prod

### Install without docker-compose
* copy ``.env.dist`` to ``.env`` and fill with the required parameters

* ``composer install`` To install composer dependencies

* ``bower install`` To install bower ressources

* ``yarn install`` To install yarn ressources

* ``yarn run dev|build`` To compile the ressources for dev or prod

### Finally
You are ready to go !

