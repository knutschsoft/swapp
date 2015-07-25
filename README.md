SWAPP
========================
<a href="https://travis-ci.org/knutschsoft/swapp" target='_blank'>
  <img title="build status of swapp" src="https://travis-ci.org/knutschsoft/swapp.svg">
</a>
[![Dependency Status](https://www.versioneye.com/user/projects/55a4aaac3863340023000001/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55a4aaac3863340023000001)
[![Dependency Status](https://www.versioneye.com/user/projects/55a4aaed386334002000006d/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55a4aaed386334002000006d)
[![Dependency Status](https://www.versioneye.com/user/projects/55a4ae92386334001a00001d/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55a4ae92386334001a00001d)

## How to start dev action?

### Install Database:

On your MySQL console:

```
CREATE DATABASE swapp;
GRANT ALL ON swapp.* TO swapp@'localhost' IDENTIFIED BY 'pa$$word';
```

### Configure ACL

How to install and configure acl: http://wiki.ubuntuusers.de/ACL
Open a terminal and type in the following commands:

```
cd /your/symfony/dir
sudo setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX app/cache app/logs
sudo setfacl -dR -m u:www-data:rwX -m u:`whoami`:rwX app/cache app/logs
```

### Setup webserver

#### Create ```/etc/apache2/sites-available/swapp.conf``` as root with the following content.

```
<VirtualHost *:80>
    ServerName swapp
    ServerAlias swapp.localhost
 
    DocumentRoot /your/symfony/dir/web
 
    <Directory />
        DirectoryIndex app.php
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    <Directory "/your/symfony/dir/web">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
 
    ErrorLog ${APACHE_LOG_DIR}/swapp_error.log
    CustomLog ${APACHE_LOG_DIR}/swapp_access.log combined
 
</VirtualHost>
```

#### run in terminal

```
sudo a2ensite swapp.conf
sudo service apache reload
```

#### add in ```/etc/hosts```

```
127.0.0.1 swapp.localhost swapp
```

### install composer and composer packages

```
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

### Execute Migrations

```php app/console doctrine:migrations:migrate --no-interaction```

### check config

Call in terminal:
```
php app/check.php
```

### install assets and start watcher

```
sudo apt-get install nodejs-legacy

npm install
./node_modules/.bin/bower install
./node_modules/.bin/gulp watch
```

### Open in browser:

```
http://swapp/config.php
```

### Entrance:

http://swapp/admin
http://swapp/eadmin
http://swapp/walks

http://swapp/app_dev.php/admin
http://swapp/app_dev.php/eadmin
http://swapp/app_dev.php/walks
