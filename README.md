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
[Open horse-calender-site ](http://Hest-Time-Controller.local.itkdev.dk/)
```shell
# 5 (optional) mac terminal command
open http://Hest-Time-Controller.local.itkdev.dk/
```

### helpful commands
```shell

## to miggrate new data to database
docker compose exec phpfpm bin/console doctrine:migration:migrate
## to initiate 

    docker compose exec phpfpm bin/console doctrine:schema:drop --full-database --force; 
    docker compose exec phpfpm bin/console doctrine:migration:migrate
    docker compose exec phpfpm bin/console create-user
    docker compose exec phpfpm bin/console create-client
    docker compose exec phpfpm bin/console create-project
    docker compose exec phpfpm bin/console create-team "Pegasus Team" a@a.com b@b.com --projectName="Project Pegasus"
    docker compose exec phpfpm bin/console create-todo 1 "Storyboard Development"  "2024-11-20" "2024-11-21"
    docker compose exec phpfpm bin/console create-timelog "admin" 1 2 30 "2024-11-22" "Completed the storyboard initial draft"
    docker compose exec phpfpm bin/console create-timelog "admin" 1 1 30 "2024-11-20" "Completed the storyboard initial draft 2"

```

### helpful tips 
- syntax when install is always= docker compose exec phpfpm composer <command> <command>
- syntax when running command in docker is always dockker compose exec phpfpm bin/console <command> <command>

