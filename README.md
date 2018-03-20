# Hackathon ESGI

## Install using docker-compose
* copy ``docker-compose.yml.dist`` to ``docker-compose.yml``

* copy ``.env.dist`` to ``.env``

* Run ``docker-compose up`` to build the network 

* ``docker-compose exec php-fpm composer install`` To install composer dependencies

* ``docker-compose exec nodejs bower install --allow-root`` To install bower ressources

### Install without docker-compose
* copy ``.env.dist`` to ``.env``

* ``composer install`` To install composer dependencies

* ``bower install`` To install bower ressources


### Finally
You are ready to go !

