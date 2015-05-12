SWAPP
========================


How to start dev action?
--------------

1. Install Database:
On your MySQL console:
```
CREATE DATABASE swapp;
GRANT ALL ON swapp.* TO swapp@'localhost' IDENTIFIED BY 'pa$$word';
```

2. Configure ACL
How to install and configure acl: http://wiki.ubuntuusers.de/ACL
Open a terminal and type in the following commands:
```
cd /your/symfony/dir
sudo setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX app/cache app/logs
sudo setfacl -dR -m u:www-data:rwX -m u:`whoami`:rwX app/cache app/logs
```

3. Setup webserver

3.1. Create ```/etc/apache2/sites-available/swapp.conf``` as root with the following content.

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

3.2. run in terminal

```
sudo a2ensite swapp.conf
sudo service apache reload
```

3.3. add in ```/etc/hosts```

```
127.0.0.1 swapp.localhost swapp
```

4. install assets

```
php app/console assets:install web --symlink
```

5. check config

Call in terminal:
```
php app/check.php
```

Open in browser:
```
http://swapp/config.php
```

