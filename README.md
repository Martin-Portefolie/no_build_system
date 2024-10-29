## dev
### config

Make sure to check [symfony requirements](https://symfony.com/doc/current/setup.html) , and install and start [docker desktop](https://www.docker.com/products/docker-desktop/).



### step-by-step setup

```shell
# 1. (always) start docker
docker compose up -d
# 2(one-time-required) install dependencies
docker compose exec phpfpm composer install
# 3(one-time-required) migrate database.
docker compose exec phpfpm bin/console doctrine:migrations:migrate
# 4 (always) initiate tailwind
docker compose exec phpfpm bin/console tailwind:build --watch --poll

```
####  open site
[Open horse-shoes-site ](http://horseshoes.local.itkdev.dk/)
```shell
# 5 (optional) mac terminal command
open http://horseshoes.local.itkdev.dk/
```

### helpful commands
```shell

## to miggrate new data to database
docker compose exec phpfpm bin/console doctrine:migration:migrate
## to initiate 

docker-compose exec phpfpm bin/console doctrine:schema:drop --full-database --force; 
docker compose exec phpfpm bin/console doctrine:migration:migrate
docker compose exec phpfpm bin/console create-user

```

### helpful tips 
- syntax when install is always= docker compose exec phpfpm composer <command> <command>
- syntax when running command in docker is always dockker compose exec phpfpm bin/console <command> <command>

