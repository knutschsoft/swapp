SWAPP
========================
<a href="https://travis-ci.org/knutschsoft/swapp" target='_blank'>
  <img title="build status of swapp" src="https://travis-ci.org/knutschsoft/swapp.svg">
</a>

## How to start dev action?

### Install Database:

On your MySQL console:

```
CREATE DATABASE swapp;
GRANT ALL ON swapp.* TO swapp@'localhost' IDENTIFIED BY 'pa$$word';
```

### Execute Migrations

```php app/console doctrine:migrations:migrate --no-interaction```

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

### install assets

```
npm install
./node_modules/.bin/gulp watch
```

### check config

Call in terminal:
```
php app/check.php
```

Open in browser:
```
http://swapp/config.php
```

