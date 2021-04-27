SWAPP
========================

[![build status of swapp](https://img.shields.io/travis/knutschsoft/swapp/master?style=flat-square&logo=travis)](https://travis-ci.org/knutschsoft/swapp)
[![StyleCi](https://styleci.io/repos/34905820/shield?branch=master&style=plasti)](https://styleci.io/repos/34905820)
[![Docker build](https://img.shields.io/docker/automated/knutschsoft/swapp?style=flat-square)](https://hub.docker.com/r/knutschsoft/swapp)
[![Docker build](https://img.shields.io/docker/build/knutschsoft/swapp?style=flat-square)](https://hub.docker.com/r/knutschsoft/swapp)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
![PHP7 Compatible](https://img.shields.io/travis/php-v/knutschsoft/swapp/master?style=flat-square)
[![Greenkeeper badge](https://badges.greenkeeper.io/knutschsoft/swapp.svg)](https://greenkeeper.io/)
[![Open Issues](https://img.shields.io/github/issues-raw/knutschsoft/swapp?style=flat-square)](https://github.com/knutschsoft/swapp/issues)
[![Closed Issues](https://img.shields.io/github/issues-closed-raw/knutschsoft/swapp?style=flat-square)](https://github.com/knutschsoft/swapp/issues?q=is%3Aissue+is%3Aclosed)
[![Contributors](https://img.shields.io/github/contributors/knutschsoft/swapp?style=flat-square)](https://github.com/knutschsoft/swapp/graphs/contributors)
![Contributors](https://img.shields.io/maintenance/yes/2020?style=flat-square)

# Entrance:

 http://swapp/eadmin

 http://swapp/walks

 http://swapp/app_dev.php/eadmin

 http://swapp/app_dev.php/walks

# If you want to use Docker

## Prepare host (initial setup)

* extend `/etc/hosts`
    ```bash
    $ sudo vi /etc/hosts 
  
    ...
    127.0.0.1 swapp
    ```
     
* copy docker .env file

    ```bash
    $ copy .env.docker.dist .env.docker 
    ```
    
* build container once and start container immediately

    ```bash
    $ docker-compose up --build web
    ```
    
## Development with docker containers

* All commands have to be executed in workspace (symfony project root)
* Start all containers in background
    ```bash
    $ docker-compose up -d web
    ```
* Install vendors, execute migrations and load fixtures (only at first time) 
    * Install vendors
        ```bash
        $ docker-compose exec web php composer.phar install
        ```
    * Execute migrations
        ```bash
        $ docker-compose exec web php bin/console doctrine:migrations:migrate -n
        ```
    * Load fixtures
        ```bash
        $ docker-compose exec web php bin/console hautelook_alice:doctrine:fixtures:load -n
        ```
## If you want to use XDebug

* Activate XDebug in `.env.docker`:
    ```
    ...
    PHP_XDEBUG_ENABLED: 1
    ...
    ```
* PHPStorm setup:
    * Settings... -> Languages & Frameworks -> PHP -> Servers: Add
        * name: has to be same as PHP_IDE_CONFIG value
        * port: 80
        * path-mapping: path of project root in host system 
    * Setting -> Languages & Frameworks -> PHP -> Debug -> DBGp Proxy:
        * `Port`: 9000
* Start containers:
    ```bash
    $ docker-compose up -d web
    ```
* After clicking "Start Listening for PHP Debug Connections" in PHPStorm you can jump to web and cli breakpoints.
* To activate/deactivate XDebug simply adjust ENV-Variable `PHP_XDEBUG_ENABLED` in `docker-compose.yml`
and restart containers (`docker-compose down && docker-compose up -d`) 

### create your own JWT token

```bash
mkdir -p config/jwt
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
```
```bash
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

### Setting the max_uploaded_filesize

* head to ./init-container.sh
* in section `Prepare PHP` change the line
    `sh -c "echo 'upload_max_filesize = 10M'` according to your needs
* if you want to allow bigger image files you have to adjust this php.ini setting as well as the `File` constraint's `maxSize` option on the imageFile property in `WayPoint.php`
    
### Cheat Sheet

* Execute symfony command
    ```bash
    $ docker-compose exec web php bin/console [SF-CONSOLE-COMMAND]
    ```
* Start webpack encore
    ```bash
    $ docker-compose exec web node_modules/.bin/encore dev-server
    ```
* Show containers and their status
    ```bash
    $ docker-compose ps
    ```
* Container shell access
    ```bash
    $ docker-compose exec web bash
    ```
* CLI connection to MySQL:
    ```bash
    $ mysql -u surveythor_demo -p
    ```
* Stop services/container
    ```bash
    $ docker-compose stop
    ```
* Stop and delete container (incl. volumes, images und networks except data volumes)
    ```bash
    $ docker-compose down
    ```

How to run EndToEnd-Tests
==========================

After booting container you can start EndToEnd-Tests with:
```BASH
$ docker-compose exec web vendor/bin/phpunit --filter EndToEnd
```
If you want to watch EndToEnd-Tests then you have to start a vnc viewer and connect to 0.0.0.0:5900 for firefox 
and 0.0.0.0:5901 for chrome. Maybe you have to set color depth to at least 15 bit.

Use Sauce Labs locally
----------------------

Install:
https://wiki.saucelabs.com/display/DOCS/Basic+Sauce+Connect+Proxy+Setup

After download and extraction start sauce labs from outside of docker (currently there is no docker container support):
```BASH
$ sc-4.4.8-linux/bin/sc -u username -k api_key -i my-tun2 --se-port 4446
```

Start tests:
```BASH
$ docker-compose exec web vendor/bin/phpunit
```

Add git hooks
=============

Execute once:
```vendor/bin/captainhook install -f -s```

E.g. a hook file will look like:  
```
docker@e5b8a0355c16:/var/www/html$ cat .git/hooks/commit-msg
#!/usr/bin/env bash
docker-compose exec --user=docker -T web ./vendor/bin/captainhook hook:commit-msg "$@"

```
