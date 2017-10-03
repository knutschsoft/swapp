#!/usr/bin/env bash

##############################################################
# wait for es services to be available
##############################################################

# enable/disable xdebug according to docker environment var
if [ "$PHP_XDEBUG_ENABLED" -eq "0" ]; then
    sed -i -e 's/zend_extension/;zend_extension/g' /usr/local/etc/php/conf.d/xdebug.ini
else
    sed -i -e "s/xdebug\.remote_host.*/xdebug.remote_host=$PHP_XDEBUG_REMOTE_HOST/g" /usr/local/etc/php/conf.d/xdebug.ini
fi

# cleanup is a good thing
rm -rf /var/www/html/var/cache/*

HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var
setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var

# init/update composer
if [ ! -e composer.phar ]; then
    EXPECTED_SIGNATURE=$(wget -q -O - https://composer.github.io/installer.sig)
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    ACTUAL_SIGNATURE=$(php -r "echo hash_file('SHA384', 'composer-setup.php');")

    if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]
    then
        >&2 echo 'ERROR: Invalid installer signature'
        rm composer-setup.php
        exit 1
    fi

    php composer-setup.php --quiet
    RESULT=$?
    rm composer-setup.php
    exit $RESULT
else
    php composer.phar self-update
fi

# ###############################################
# Init Project
# ###############################################
if [ ! -d vendor ]; then
    php composer.phar install

    php bin/console doctrine:database:drop --force
    php bin/console doctrine:database:create -n
    php bin/console doctrine:schema:update --force
fi

##############################################################
# execute the default command
##############################################################
apache2-foreground